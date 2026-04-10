# Unsafe Rust

## Understanding Unsafe Rust

### What is Unsafe Rust?
```rust
// Safe Rust - compiler guarantees memory safety
fn safe_rust() {
    let x = 5;
    let y = x; // x is copied because i32 implements Copy
    println!("x: {}, y: {}", x, y); // Both are valid
}

// Unsafe Rust - programmer must guarantee safety
fn unsafe_rust() {
    let mut x = 5;
    let r1 = &x as *const i32;
    let r2 = &mut x as *mut i32;
    
    unsafe {
        println!("r1: {}", *r1);
        *r2 = 10;
        println!("r1 after modification: {}", *r1);
    }
}

// When to use unsafe
fn when_to_use_unsafe() {
    // 1. Interfacing with hardware
    // 2. Implementing low-level data structures
    // 3. Performance optimizations
    // 4. Calling foreign functions (FFI)
    // 5. Accessing union fields
    
    // Example: Performance optimization
    let numbers = vec![1, 2, 3, 4, 5];
    let sum = unsafe {
        // Unsafe for performance - direct memory access
        let ptr = numbers.as_ptr();
        let len = numbers.len();
        let mut sum = 0;
        
        for i in 0..len {
            sum += *ptr.add(i);
        }
        
        sum
    };
    
    println!("Sum: {}", sum);
}
```

### Unsafe Functions and Blocks
```rust
// Unsafe function
unsafe fn dangerous_function() {
    println!("This function is unsafe");
}

// Calling unsafe function
fn call_unsafe_function() {
    unsafe {
        dangerous_function();
    }
}

// Unsafe function with parameters
unsafe fn add_raw(ptr1: *const i32, ptr2: *const i32) -> i32 {
    *ptr1 + *ptr2
}

fn use_unsafe_function() {
    let x = 10;
    let y = 20;
    
    let result = unsafe {
        add_raw(&x as *const i32, &y as *const i32)
    };
    
    println!("Result: {}", result);
}

// Unsafe trait
unsafe trait UnsafeTrait {
    fn unsafe_method(&self);
}

unsafe impl UnsafeTrait for i32 {
    fn unsafe_method(&self) {
        println!("Unsafe method called on {}", self);
    }
}

fn use_unsafe_trait() {
    let x = 42;
    
    unsafe {
        x.unsafe_method();
    }
}
```

## Raw Pointers

### Raw Pointer Basics
```rust
// Creating raw pointers
fn raw_pointers() {
    let mut num = 5;
    
    // Immutable raw pointer
    let r1 = &num as *const i32;
    
    // Mutable raw pointer
    let r2 = &mut num as *mut i32;
    
    unsafe {
        println!("r1 points to: {}", *r1);
        println!("r2 points to: {}", *r2);
        
        // Dereferencing mutable pointer
        *r2 = 10;
        println!("After modification: {}", *r1);
    }
}

// Pointer arithmetic
fn pointer_arithmetic() {
    let numbers = [1, 2, 3, 4, 5];
    let ptr = numbers.as_ptr();
    
    unsafe {
        for i in 0..numbers.len() {
            println!("numbers[{}] = {}", i, *ptr.add(i));
        }
        
        // Pointer subtraction
        let last_ptr = ptr.add(numbers.len() - 1);
        let offset = last_ptr.offset_from(ptr);
        println!("Offset: {}", offset);
    }
}

// Null pointers
fn null_pointers() {
    let null_ptr: *const i32 = std::ptr::null();
    
    unsafe {
        if null_ptr.is_null() {
            println!("Pointer is null");
        }
        
        // This would be undefined behavior
        // println!("{}", *null_ptr);
    }
}

// Pointer to pointer
fn pointer_to_pointer() {
    let x = 42;
    let ptr = &x as *const i32;
    let ptr_to_ptr = &ptr as *const *const i32;
    
    unsafe {
        println!("x: {}", x);
        println!("ptr: {:p}", ptr);
        println!("ptr_to_ptr: {:p}", ptr_to_ptr);
        println!("Dereferenced ptr_to_ptr: {:p}", *ptr_to_ptr);
        println!("Double dereferenced: {}", **ptr_to_ptr);
    }
}
```

### Advanced Pointer Operations
```rust
// Casting between pointer types
fn pointer_casting() {
    let x: u32 = 0x12345678;
    let ptr = &x as *const u32;
    
    unsafe {
        // Cast to different type
        let byte_ptr = ptr as *const u8;
        println!("First byte: 0x{:02x}", *byte_ptr);
        
        // Cast back
        let u32_ptr = byte_ptr as *const u32;
        println!("Back to u32: 0x{:08x}", *u32_ptr);
    }
}

// Pointer alignment
fn pointer_alignment() {
    let numbers = [1u8, 2u8, 3u8, 4u8];
    let ptr = numbers.as_ptr();
    
    unsafe {
        println!("Pointer alignment: {}", ptr.align_offset(std::mem::align_of::<u32>()));
        
        // Align pointer
        let aligned_ptr = ptr.add(ptr.align_offset(std::mem::align_of::<u32>()));
        println!("Aligned pointer: {:p}", aligned_ptr);
    }
}

// Volatile operations
fn volatile_operations() {
    let mut x = 42;
    let ptr = &mut x as *mut i32;
    
    unsafe {
        // Volatile read
        let value = std::ptr::read_volatile(ptr);
        println!("Volatile read: {}", value);
        
        // Volatile write
        std::ptr::write_volatile(ptr, 100);
        println!("After volatile write: {}", x);
    }
}

// Uninitialized memory
fn uninitialized_memory() {
    use std::mem::MaybeUninit;
    
    // Safe way to handle uninitialized memory
    let mut data: MaybeUninit<i32> = MaybeUninit::uninit();
    
    unsafe {
        // Initialize the value
        data.as_mut_ptr().write(42);
        
        // Assume it's initialized
        let value = data.assume_init();
        println!("Initialized value: {}", value);
    }
}
```

## Unsafe Functions and Traits

### Unsafe Functions
```rust
// Function that requires caller to ensure safety
unsafe fn create_slice_from_raw_parts(ptr: *const i32, len: usize) -> &'static [i32] {
    std::slice::from_raw_parts(ptr, len)
}

fn use_create_slice() {
    let data = [1, 2, 3, 4, 5];
    let ptr = data.as_ptr();
    
    unsafe {
        let slice = create_slice_from_raw_parts(ptr, data.len());
        println!("Slice: {:?}", slice);
    }
}

// Function with unsafe implementation but safe interface
fn safe_wrapper_unsafe_impl() {
    fn safe_function(data: &[i32]) -> Option<i32> {
        if data.is_empty() {
            return None;
        }
        
        unsafe {
            // Unsafe implementation
            let ptr = data.as_ptr();
            Some(*ptr)
        }
    }
    
    let numbers = [10, 20, 30];
    match safe_function(&numbers) {
        Some(first) => println!("First element: {}", first),
        None => println!("Empty slice"),
    }
}

// Function that requires specific preconditions
unsafe fn transmute_example() {
    let x: u32 = 0x12345678;
    let y: f32 = std::mem::transmute(x);
    println!("Transmuted value: {}", y);
}

fn transmute_safely() {
    // This is dangerous - only use when you know what you're doing
    unsafe {
        transmute_example();
    }
}
```

### Unsafe Traits
```rust
// Unsafe trait for types that can be sent between threads
unsafe trait SendMarker {}

// Implementing unsafe trait
unsafe impl SendMarker for String {}

// Safe wrapper around unsafe operations
struct SafeWrapper<T> {
    data: T,
}

impl<T> SafeWrapper<T> {
    fn new(data: T) -> Self {
        SafeWrapper { data }
    }
    
    fn get(&self) -> &T {
        &self.data
    }
    
    fn get_mut(&mut self) -> &mut T {
        &mut self.data
    }
}

// Unsafe trait implementation
unsafe impl<T> Send for SafeWrapper<T> where T: Send {}
unsafe impl<T> Sync for SafeWrapper<T> where T: Sync {}

fn use_safe_wrapper() {
    let wrapper = SafeWrapper::new(42);
    println!("Wrapper value: {}", wrapper.get());
}

// Custom unsafe trait
unsafe trait UnsafeIterator {
    unsafe fn next_unsafe(&mut self) -> Option<&i32>;
}

struct UnsafeVec {
    data: Vec<i32>,
    index: usize,
}

impl UnsafeVec {
    fn new(data: Vec<i32>) -> Self {
        UnsafeVec { data, index: 0 }
    }
}

unsafe impl UnsafeIterator for UnsafeVec {
    unsafe fn next_unsafe(&mut self) -> Option<&i32> {
        if self.index < self.data.len() {
            let ptr = self.data.as_ptr();
            let item = ptr.add(self.index);
            self.index += 1;
            Some(&*item)
        } else {
            None
        }
    }
}

fn use_unsafe_iterator() {
    let data = vec![1, 2, 3, 4, 5];
    let mut iter = UnsafeVec::new(data);
    
    unsafe {
        while let Some(item) = iter.next_unsafe() {
            println!("Item: {}", item);
        }
    }
}
```

## Unions

### Union Basics
```rust
// Basic union definition
union MyUnion {
    f1: u32,
    f2: f32,
}

fn basic_union() {
    let mut u = MyUnion { f1: 1 };
    
    unsafe {
        u.f1 = 0x3f800000; // 1.0 in IEEE 754
        println!("u.f1: {}", u.f1);
        println!("u.f2: {}", u.f2);
    }
}

// Union with different types
union MixedUnion {
    i: i32,
    u: u32,
    f: f32,
}

fn mixed_union() {
    let mut u = MixedUnion { i: -1 };
    
    unsafe {
        println!("As i32: {}", u.i);
        println!("As u32: {}", u.u);
        println!("As f32: {}", u.f);
        
        u.f = 1.0;
        println!("After setting f32:");
        println!("As i32: {}", u.i);
        println!("As u32: {}", u.u);
        println!("As f32: {}", u.f);
    }
}

// Union with arrays
union ArrayUnion {
    bytes: [u8; 4],
    word: u32,
}

fn array_union() {
    let mut u = ArrayUnion { word: 0x12345678 };
    
    unsafe {
        println!("Word: 0x{:08x}", u.word);
        println!("Bytes: {:02x} {:02x} {:02x} {:02x}", 
                 u.bytes[0], u.bytes[1], u.bytes[2], u.bytes[3]);
        
        u.bytes[0] = 0x78;
        u.bytes[1] = 0x56;
        u.bytes[2] = 0x34;
        u.bytes[3] = 0x12;
        
        println!("After byte swap:");
        println!("Word: 0x{:08x}", u.word);
    }
}
```

### Union with Methods
```rust
// Union with methods
union ValueUnion {
    integer: i64,
    float: f64,
    pointer: *const (),
}

impl ValueUnion {
    fn new_integer(value: i64) -> Self {
        ValueUnion { integer: value }
    }
    
    fn new_float(value: f64) -> Self {
        ValueUnion { float: value }
    }
    
    fn new_pointer(value: *const ()) -> Self {
        ValueUnion { pointer: value }
    }
    
    unsafe fn as_integer(&self) -> i64 {
        self.integer
    }
    
    unsafe fn as_float(&self) -> f64 {
        self.float
    }
    
    unsafe fn as_pointer(&self) -> *const () {
        self.pointer
    }
}

fn union_with_methods() {
    let v1 = ValueUnion::new_integer(42);
    let v2 = ValueUnion::new_float(3.14159);
    let v3 = ValueUnion::new_pointer(&42 as *const () as *const ());
    
    unsafe {
        println!("Integer: {}", v1.as_integer());
        println!("Float: {}", v2.as_float());
        println!("Pointer: {:p}", v3.as_pointer());
    }
}

// Tagged union (enum with union)
#[derive(Debug)]
enum TaggedValue {
    Integer(i64),
    Float(f64),
    String(String),
}

fn tagged_union() {
    let values = vec![
        TaggedValue::Integer(42),
        TaggedValue::Float(3.14159),
        TaggedValue::String("Hello".to_string()),
    ];
    
    for value in values {
        match value {
            TaggedValue::Integer(i) => println!("Integer: {}", i),
            TaggedValue::Float(f) => println!("Float: {}", f),
            TaggedValue::String(s) => println!("String: {}", s),
        }
    }
}
```

## Foreign Function Interface (FFI)

### Calling C Functions
```rust
// Calling C functions (requires linking with C library)
/*
extern "C" {
    fn abs(input: i32) -> i32;
    fn strlen(s: *const u8) -> usize;
}

fn call_c_functions() {
    unsafe {
        let x = -42;
        let abs_x = abs(x);
        println!("abs({}) = {}", x, abs_x);
        
        let s = b"Hello, C!";
        let len = strlen(s.as_ptr());
        println!("Length: {}", len);
    }
}
*/

// Calling Rust from C
/*
#[no_mangle]
pub extern "C" fn add(a: i32, b: i32) -> i32 {
    a + b
}

#[no_mangle]
pub extern "C" fn greet(name: *const u8) {
    unsafe {
        let name_str = std::ffi::CStr::from_ptr(name as *const i8);
        println!("Hello, {}!", name_str.to_str().unwrap());
    }
}
*/

// Callbacks
/*
extern "C" {
    fn qsort(base: *mut void, nmemb: size_t, size: size_t, compar: *mut void);
}

fn use_qsort() {
    let mut numbers = [5, 2, 8, 1, 9];
    
    unsafe {
        qsort(
            numbers.as_mut_ptr() as *mut void,
            numbers.len(),
            std::mem::size_of::<i32>(),
            compare_integers as *mut void,
        );
    }
    
    println!("Sorted: {:?}", numbers);
}

extern "C" fn compare_integers(a: *const void, b: *const void) -> i32 {
    unsafe {
        let a = *(a as *const i32);
        let b = *(b as *const i32);
        a.cmp(&b) as i32
    }
}
*/
```

### C Strings
```rust
// Working with C strings
use std::ffi::{CStr, CString};

fn c_string_operations() {
    // Rust string to C string
    let rust_string = "Hello, C!";
    let c_string = CString::new(rust_string).unwrap();
    
    unsafe {
        // C string to Rust string
        let c_str = CStr::from_ptr(c_string.as_ptr());
        let back_to_rust = c_str.to_str().unwrap();
        
        println!("Original: {}", rust_string);
        println!("C string: {:?}", c_string);
        println!("Back to Rust: {}", back_to_rust);
    }
}

// C string with null bytes
fn c_string_with_null() {
    // This would panic
    // let bad_string = CString::new("Hello\0World").unwrap();
    
    // Safe way to handle strings with null bytes
    let string_with_null = "Hello\0World";
    let bytes = string_with_null.as_bytes();
    
    unsafe {
        let c_str = CStr::from_bytes_with_nul_unchecked(bytes);
        println!("C string with null: {:?}", c_str);
    }
}

// C string from bytes
fn c_string_from_bytes() {
    let bytes = b"Hello, C!\0";
    
    unsafe {
        let c_str = CStr::from_bytes_with_nul_unchecked(bytes);
        let rust_string = c_str.to_str().unwrap();
        println!("From bytes: {}", rust_string);
    }
}
```

## Memory Management

### Manual Memory Management
```rust
// Manual allocation
fn manual_allocation() {
    use std::alloc::{alloc, dealloc, Layout};
    
    unsafe {
        // Allocate memory
        let layout = Layout::from_size_align_unchecked(
            std::mem::size_of::<i32>(),
            std::mem::align_of::<i32>(),
        );
        
        let ptr = alloc(layout);
        
        if ptr.is_null() {
            println!("Allocation failed");
            return;
        }
        
        // Write to memory
        *ptr = 42;
        println!("Value: {}", *ptr);
        
        // Deallocate memory
        dealloc(ptr, layout);
    }
}

// Custom allocator
struct CustomAllocator;

unsafe impl std::alloc::GlobalAlloc for CustomAllocator {
    unsafe fn alloc(&self, layout: std::alloc::Layout) -> *mut u8 {
        std::alloc::System.alloc(layout)
    }
    
    unsafe fn dealloc(&self, ptr: *mut u8, layout: std::alloc::Layout) {
        std::alloc::System.dealloc(ptr, layout);
    }
}

// Use custom allocator
/*
#[global_allocator]
static GLOBAL: CustomAllocator = CustomAllocator;
*/

fn custom_allocator_example() {
    let x = Box::new(42);
    println!("Boxed value: {}", x);
}

// Memory pool
struct MemoryPool {
    pool: Vec<u8>,
    used: usize,
}

impl MemoryPool {
    fn new(size: usize) -> Self {
        MemoryPool {
            pool: vec![0; size],
            used: 0,
        }
    }
    
    fn allocate(&mut self, size: usize, align: usize) -> Option<*mut u8> {
        let start = self.pool.as_ptr() as usize;
        let aligned = (start + self.used + align - 1) & !(align - 1);
        let offset = aligned - start;
        
        if offset + size <= self.pool.len() {
            self.used = offset + size;
            Some(aligned as *mut u8)
        } else {
            None
        }
    }
    
    fn reset(&mut self) {
        self.used = 0;
    }
}

fn memory_pool_example() {
    let mut pool = MemoryPool::new(1024);
    
    unsafe {
        if let Some(ptr) = pool.allocate(4, 4) {
            *(ptr as *mut i32) = 42;
            println!("Pool allocated value: {}", *(ptr as *mut i32));
        }
        
        pool.reset();
    }
}
```

### Stack and Heap Manipulation
```rust
// Stack manipulation
fn stack_manipulation() {
    // Moving data to heap
    let stack_data = [1, 2, 3, 4, 5];
    let heap_data = stack_data.to_vec();
    
    println!("Stack data: {:?}", stack_data);
    println!("Heap data: {:?}", heap_data);
    
    // Moving data back to stack (if possible)
    let stack_data_again: [i32; 5] = heap_data.try_into().unwrap_or([0; 5]);
    println!("Back to stack: {:?}", stack_data_again);
}

// Direct stack manipulation
fn direct_stack_manipulation() {
    let mut x = 42;
    let ptr = &mut x as *mut i32;
    
    unsafe {
        // Directly modify stack variable
        *ptr = 100;
        println!("Modified x: {}", x);
    }
}

// Heap manipulation
fn heap_manipulation() {
    use std::alloc::{alloc, dealloc, Layout};
    
    unsafe {
        // Allocate on heap
        let layout = Layout::array::<i32>(5).unwrap();
        let ptr = alloc(layout) as *mut i32;
        
        // Initialize
        for i in 0..5 {
            *ptr.add(i) = (i + 1) * 10;
        }
        
        // Read
        for i in 0..5 {
            println!("heap[{}] = {}", i, *ptr.add(i));
        }
        
        // Deallocate
        dealloc(ptr as *mut u8, layout);
    }
}
```

## Performance Optimizations

### Zero-Cost Abstractions
```rust
// Zero-cost abstraction example
fn zero_cost_abstraction() {
    // Iterator optimization
    let numbers = vec![1, 2, 3, 4, 5];
    let sum: i32 = numbers.iter().map(|x| x * 2).sum();
    
    println!("Sum: {}", sum);
    
    // Manual loop (should be similar performance)
    let mut manual_sum = 0;
    for x in &numbers {
        manual_sum += x * 2;
    }
    
    println!("Manual sum: {}", manual_sum);
}

// Inlining optimization
#[inline(always)]
fn always_inline(x: i32) -> i32 {
    x * 2
}

#[inline(never)]
fn never_inline(x: i32) -> i32 {
    x * 2
}

fn inlining_example() {
    let x = 42;
    
    let result1 = always_inline(x);
    let result2 = never_inline(x);
    
    println!("Always inline: {}", result1);
    println!("Never inline: {}", result2);
}

// SIMD optimization
fn simd_optimization() {
    use std::arch::x86_64::*;
    
    let a = [1.0, 2.0, 3.0, 4.0];
    let b = [5.0, 6.0, 7.0, 8.0];
    
    unsafe {
        if is_x86_feature_detected!("avx") {
            let va = _mm256_loadu_ps(a.as_ptr());
            let vb = _mm256_loadu_ps(b.as_ptr());
            let result = _mm256_add_ps(va, vb);
            
            let mut output = [0.0; 4];
            _mm256_storeu_ps(output.as_mut_ptr(), result);
            
            println!("SIMD result: {:?}", output);
        } else {
            let result: Vec<f32> = a.iter().zip(b.iter()).map(|(x, y)| x + y).collect();
            println!("Scalar result: {:?}", result);
        }
    }
}
```

### Memory Layout Optimization
```rust
// Memory layout optimization
fn memory_layout_optimization() {
    // Bad: struct with padding
    #[repr(C)]
    struct BadStruct {
        a: u8,    // 1 byte + 3 bytes padding
        b: u32,   // 4 bytes
        c: u8,    // 1 byte + 3 bytes padding
        d: u32,   // 4 bytes
    }
    
    // Good: optimized layout
    #[repr(C)]
    struct GoodStruct {
        b: u32,   // 4 bytes
        d: u32,   // 4 bytes
        a: u8,    // 1 byte
        c: u8,    // 1 byte + 2 bytes padding
    }
    
    println!("BadStruct size: {}", std::mem::size_of::<BadStruct>());
    println!("GoodStruct size: {}", std::mem::size_of::<GoodStruct>());
}

// Cache-friendly data structures
fn cache_friendly_structures() {
    // Array of structs (AoS)
    #[derive(Debug)]
    struct Point {
        x: f64,
        y: f64,
        z: f64,
    }
    
    let points_aos: Vec<Point> = vec![
        Point { x: 1.0, y: 2.0, z: 3.0 },
        Point { x: 4.0, y: 5.0, z: 6.0 },
        Point { x: 7.0, y: 8.0, z: 9.0 },
    ];
    
    // Struct of arrays (SoA)
    #[derive(Debug)]
    struct Points {
        x: Vec<f64>,
        y: Vec<f64>,
        z: Vec<f64>,
    }
    
    let points_soa = Points {
        x: vec![1.0, 4.0, 7.0],
        y: vec![2.0, 5.0, 8.0],
        z: vec![3.0, 6.0, 9.0],
    };
    
    println!("AoS: {:?}", points_aos);
    println!("SoA: {:?}", points_soa);
}

// Memory pool for performance
struct FastMemoryPool {
    chunks: Vec<*mut u8>,
    current_chunk: usize,
    chunk_size: usize,
    used: usize,
}

impl FastMemoryPool {
    fn new(chunk_size: usize) -> Self {
        FastMemoryPool {
            chunks: Vec::new(),
            current_chunk: 0,
            chunk_size,
            used: 0,
        }
    }
    
    fn allocate(&mut self, size: usize) -> Option<*mut u8> {
        if self.used + size > self.chunk_size {
            // Allocate new chunk
            let new_chunk = unsafe {
                std::alloc::alloc(std::alloc::Layout::from_size_align_unchecked(
                    self.chunk_size,
                    8,
                ))
            };
            
            if new_chunk.is_null() {
                return None;
            }
            
            self.chunks.push(new_chunk);
            self.current_chunk = self.chunks.len() - 1;
            self.used = 0;
        }
        
        let chunk = self.chunks[self.current_chunk];
        let ptr = unsafe { chunk.add(self.used) };
        self.used += size;
        
        Some(ptr)
    }
}

impl Drop for FastMemoryPool {
    fn drop(&mut self) {
        for chunk in &self.chunks {
            unsafe {
                std::alloc::dealloc(
                    *chunk,
                    std::alloc::Layout::from_size_align_unchecked(self.chunk_size, 8),
                );
            }
        }
    }
}

fn fast_memory_pool_example() {
    let mut pool = FastMemoryPool::new(1024);
    
    unsafe {
        if let Some(ptr) = pool.allocate(4) {
            *(ptr as *mut i32) = 42;
            println!("Pool allocated: {}", *(ptr as *mut i32));
        }
    }
}
```

## Best Practices

### Unsafe Best Practices
```rust
// 1. Minimize unsafe code
fn minimize_unsafe() {
    // Bad: large unsafe block
    unsafe {
        let x = 5;
        let y = 10;
        let z = x + y;
        println!("Result: {}", z);
    }
    
    // Good: minimal unsafe code
    let x = 5;
    let y = 10;
    let result = unsafe {
        x + y
    };
    println!("Result: {}", result);
}

// 2. Document unsafe code
fn document_unsafe() {
    /// Adds two numbers using unsafe operations
    /// 
    /// # Safety
    /// 
    /// This function is safe because it doesn't perform any operations
    /// that could cause undefined behavior.
    unsafe fn add_unsafe(a: i32, b: i32) -> i32 {
        a + b
    }
    
    let result = unsafe { add_unsafe(5, 3) };
    println!("Result: {}", result);
}

// 3. Use safe abstractions
fn safe_abstractions() {
    // Safe wrapper around unsafe operations
    struct SafeArray<T> {
        data: Vec<T>,
    }
    
    impl<T> SafeArray<T> {
        fn new() -> Self {
            SafeArray { data: Vec::new() }
        }
        
        fn push(&mut self, item: T) {
            self.data.push(item);
        }
        
        fn get(&self, index: usize) -> Option<&T> {
            self.data.get(index)
        }
        
        unsafe fn get_unchecked(&self, index: usize) -> &T {
            self.data.get_unchecked(index)
        }
    }
    
    let mut arr = SafeArray::new();
    arr.push(42);
    arr.push(24);
    
    // Safe access
    match arr.get(0) {
        Some(value) => println!("Safe access: {}", value),
        None => println!("Index out of bounds"),
    }
    
    // Unsafe access (only when you're sure it's safe)
    unsafe {
        let value = arr.get_unchecked(1);
        println!("Unsafe access: {}", value);
    }
}

// 4. Validate preconditions
fn validate_preconditions() {
    unsafe fn safe_slice_from_parts(ptr: *const i32, len: usize) -> Option<&'static [i32]> {
        // Validate preconditions
        if ptr.is_null() || len == 0 {
            return None;
        }
        
        // Check if the memory is readable (simplified)
        // In real code, you'd need more sophisticated checks
        
        Some(std::slice::from_raw_parts(ptr, len))
    }
    
    let data = [1, 2, 3, 4, 5];
    let ptr = data.as_ptr();
    
    unsafe {
        match safe_slice_from_parts(ptr, data.len()) {
            Some(slice) => println!("Slice: {:?}", slice),
            None => println!("Invalid parameters"),
        }
    }
}

// 5. Use RAII for resource management
fn raii_resource_management() {
    struct SafeBuffer {
        ptr: *mut u8,
        size: usize,
    }
    
    impl SafeBuffer {
        fn new(size: usize) -> Option<Self> {
            unsafe {
                let layout = std::alloc::Layout::from_size_align_unchecked(size, 1);
                let ptr = std::alloc::alloc(layout);
                
                if ptr.is_null() {
                    None
                } else {
                    Some(SafeBuffer { ptr, size })
                }
            }
        }
        
        unsafe fn as_slice(&self) -> &[u8] {
            std::slice::from_raw_parts(self.ptr, self.size)
        }
        
        unsafe fn as_mut_slice(&mut self) -> &mut [u8] {
            std::slice::from_raw_parts_mut(self.ptr, self.size)
        }
    }
    
    impl Drop for SafeBuffer {
        fn drop(&mut self) {
            unsafe {
                let layout = std::alloc::Layout::from_size_align_unchecked(self.size, 1);
                std::alloc::dealloc(self.ptr, layout);
            }
        }
    }
    
    if let Some(mut buffer) = SafeBuffer::new(10) {
        unsafe {
            let slice = buffer.as_mut_slice();
            slice[0] = 42;
            println!("Buffer[0] = {}", slice[0]);
        }
    }
}

// 6. Test unsafe code thoroughly
fn test_unsafe_code() {
    unsafe fn unsafe_divide(a: i32, b: i32) -> Option<i32> {
        if b == 0 {
            None
        } else {
            Some(a / b)
        }
    }
    
    // Test cases
    assert_eq!(unsafe { unsafe_divide(10, 2) }, Some(5));
    assert_eq!(unsafe { unsafe_divide(10, 0) }, None);
    
    println!("All tests passed");
}

// 7. Use compiler attributes
fn compiler_attributes() {
    #[inline(always)]
    unsafe fn always_inline_unsafe(x: i32) -> i32 {
        x * 2
    }
    
    #[cold]
    unsafe fn cold_function() {
        println!("This function is cold");
    }
    
    let result = unsafe { always_inline_unsafe(21) };
    println!("Result: {}", result);
    
    unsafe { cold_function(); }
}
```

### Safety Guidelines
```rust
// 1. Understand undefined behavior
fn undefined_behavior() {
    // Examples of undefined behavior:
    
    // 1. Dereferencing null pointer
    // let null_ptr: *const i32 = std::ptr::null();
    // unsafe { println!("{}", *null_ptr); } // UB!
    
    // 2. Use after free
    // let ptr = unsafe { std::alloc::alloc(std::alloc::Layout::new::<i32>()) };
    // unsafe { std::alloc::dealloc(ptr, std::alloc::Layout::new::<i32>()); }
    // unsafe { println!("{}", *ptr); } // UB!
    
    // 3. Data races
    // let data = std::sync::Arc::new(std::sync::Mutex::new(0));
    // let data_clone = data.clone();
    // std::thread::spawn(move || {
    //     unsafe { *data_clone.lock().unwrap() = 1; }
    // });
    // unsafe { *data.lock().unwrap() = 2; } // Potential data race!
    
    // 4. Invalid memory access
    // let ptr = 0x1000 as *const i32;
    // unsafe { println!("{}", *ptr); } // UB!
    
    println!("Undefined behavior examples commented out for safety");
}

// 2. Use tools for safety checking
fn safety_tools() {
    // Tools that help with unsafe Rust:
    // - clippy (lints for unsafe code)
    // - miri (runtime UB detection)
    // - valgrind (memory error detection)
    // - address sanitizer (ASan)
    // - thread sanitizer (TSan)
    
    println!("Use tools like clippy, miri, valgrind, ASan, and TSan");
}

// 3. Follow the unsafe guidelines
fn unsafe_guidelines() {
    // Rust unsafe guidelines:
    // 1. Don't use unsafe unless necessary
    // 2. Isolate unsafe code in small modules
    // 3. Document safety preconditions
    // 4. Provide safe abstractions
    // 5. Test thoroughly
    // 6. Use tools to verify safety
    // 7. Review unsafe code carefully
    
    println!("Follow the official Rust unsafe guidelines");
}

// 4. Use safe alternatives when possible
fn safe_alternatives() {
    // Instead of raw pointers, use references
    let x = 42;
    let r = &x;
    println!("Reference: {}", r);
    
    // Instead of manual memory management, use Box/Vec
    let boxed = Box::new(42);
    println!("Boxed: {}", boxed);
    
    // Instead of unions, use enums
    enum Value {
        Integer(i32),
        Float(f64),
    }
    
    let v = Value::Integer(42);
    match v {
        Value::Integer(i) => println!("Integer: {}", i),
        Value::Float(f) => println!("Float: {}", f),
    }
    
    // Instead of FFI, use safe wrappers
    // (Example not shown due to complexity)
    
    println!("Use safe alternatives whenever possible");
}
```

## Common Pitfalls

### Common Unsafe Mistakes
```rust
// 1. Dereferencing invalid pointers
fn invalid_pointers() {
    // Bad: dereferencing null pointer
    // let null_ptr: *const i32 = std::ptr::null();
    // unsafe { println!("{}", *null_ptr); } // UB!
    
    // Bad: dereferencing dangling pointer
    // let ptr;
    // {
    //     let x = 42;
    //     ptr = &x as *const i32;
    // }
    // unsafe { println!("{}", *ptr); } // UB!
    
    // Good: ensure pointer validity
    let x = 42;
    let ptr = &x as *const i32;
    unsafe { println!("{}", *ptr); } // Safe
}

// 2. Data races
fn data_races() {
    use std::sync::Arc;
    use std::thread;
    
    // Bad: data race with raw pointers
    let data = Arc::new(42);
    let data_ptr = Arc::into_raw(data) as *mut i32;
    
    let data_ptr_clone = data_ptr;
    thread::spawn(move || {
        unsafe { *data_ptr_clone = 100; }
    });
    
    thread::sleep(std::time::Duration::from_millis(100));
    unsafe { println!("{}", *data_ptr); } // Data race!
    
    // Good: use synchronization
    let safe_data = Arc::new(std::sync::Mutex::new(42));
    let safe_data_clone = safe_data.clone();
    
    thread::spawn(move || {
        *safe_data_clone.lock().unwrap() = 100;
    });
    
    thread::sleep(std::time::Duration::from_millis(100));
    println!("Safe: {}", *safe_data.lock().unwrap());
}

// 3. Use after free
fn use_after_free() {
    // Bad: use after free
    // let ptr = unsafe { std::alloc::alloc(std::alloc::Layout::new::<i32>()) };
    // unsafe { std::alloc::dealloc(ptr, std::alloc::Layout::new::<i32>()); }
    // unsafe { *ptr = 42; } // UB!
    
    // Good: use RAII
    let safe_ptr = Box::new(42);
    println!("Boxed: {}", safe_ptr);
}

// 4. Buffer overflow
fn buffer_overflow() {
    // Bad: buffer overflow
    // let arr = [1, 2, 3];
    // let ptr = arr.as_ptr();
    // unsafe { println!("{}", *ptr.add(3)); } // UB!
    
    // Good: bounds checking
    let arr = [1, 2, 3];
    if let Some(value) = arr.get(2) {
        println!("Value: {}", value);
    }
}

// 5. Misaligned pointer access
fn misaligned_access() {
    // Bad: misaligned access
    // let bytes = [1, 2, 3, 4];
    // let ptr = bytes.as_ptr() as *const u32;
    // unsafe { println!("{}", *ptr); } // Might be UB!
    
    // Good: ensure proper alignment
    let aligned = [1u32, 2, 3, 4];
    let ptr = aligned.as_ptr();
    unsafe { println!("{}", *ptr); } // Safe
}

// 6. Violating lifetime rules
fn lifetime_violations() {
    // Bad: returning reference to local variable
    // unsafe fn bad_function() -> &'static str {
    //     let local = "local";
    //     local
    // }
    
    // Good: return owned value or static
    fn good_function() -> String {
        "owned".to_string()
    }
    
    let result = good_function();
    println!("Result: {}", result);
}

// 7. Type punning
fn type_punning() {
    // Bad: unsafe type punning
    // let x: u32 = 0x12345678;
    // let ptr = &x as *const u32 as *const f32;
    // unsafe { println!("{}", *ptr); } // Might be UB!
    
    // Good: use transmute carefully
    let x: u32 = 0x12345678;
    let y: f32;
    unsafe {
        y = std::mem::transmute(x);
        println!("Transmuted: {}", y);
    }
}

// 8. Ignoring aliasing rules
fn aliasing_violations() {
    // Bad: violating aliasing rules
    // let mut x = 42;
    // let r1 = &x;
    // let r2 = &mut x;
    // println!("{} {}", r1, r2); // Compilation error
    
    // Good: respect borrowing rules
    let mut x = 42;
    {
        let r1 = &x;
        println!("Immutable: {}", r1);
    }
    {
        let r2 = &mut x;
        *r2 = 100;
        println!("Mutable: {}", r2);
    }
}
```

### Debugging Unsafe Code
```rust
// Debugging techniques
fn debugging_unsafe() {
    // 1. Use assertions
    unsafe fn safe_divide(a: i32, b: i32) -> i32 {
        assert!(b != 0, "Division by zero");
        a / b
    }
    
    // 2. Use logging
    unsafe fn logged_operation(ptr: *const i32) -> i32 {
        println!("Dereferencing pointer: {:p}", ptr);
        assert!(!ptr.is_null(), "Null pointer");
        let value = *ptr;
        println!("Value: {}", value);
        value
    }
    
    // 3. Use sanitizers
    // Compile with: RUSTFLAGS="-Z sanitizer=address"
    // or: RUSTFLAGS="-Z sanitizer=thread"
    
    // 4. Use miri
    // cargo +nightly miri test
    
    // 5. Use valgrind
    // valgrind ./target/debug/program
    
    println!("Use assertions, logging, sanitizers, miri, and valgrind");
}

// Testing unsafe code
fn test_unsafe_code() {
    unsafe fn unsafe_function(x: *const i32) -> i32 {
        assert!(!x.is_null(), "Null pointer");
        *x
    }
    
    // Test with valid pointer
    let x = 42;
    let result = unsafe { unsafe_function(&x) };
    assert_eq!(result, 42);
    
    // Test with null pointer (should panic)
    // let null_ptr: *const i32 = std::ptr::null();
    // unsafe { unsafe_function(null_ptr); } // Should panic
    
    println!("Unsafe function tests passed");
}

// Code review checklist
fn code_review_checklist() {
    println!("Unsafe code review checklist:");
    println!("1. Is unsafe code necessary?");
    println!("2. Are preconditions documented?");
    println!("3. Are there safe abstractions?");
    println!("4. Is pointer validity checked?");
    println!("5. Are lifetimes respected?");
    println!("6. Is synchronization used correctly?");
    println!("7. Are there tests for edge cases?");
    println!("8. Are tools used for verification?");
}
```

## Summary

Unsafe Rust provides powerful capabilities for low-level programming:

**Core Concepts:**
- `unsafe` keyword for bypassing compiler checks
- Raw pointers (`*const T`, `*mut T`) for direct memory access
- Unions for type punning
- FFI for interoperability with other languages

**Unsafe Operations:**
- Dereferencing raw pointers
- Calling unsafe functions
- Accessing union fields
- Performing bit-level operations
- Interfacing with hardware

**Memory Management:**
- Manual allocation/deallocation
- Custom allocators
- Memory pools
- Stack/heap manipulation
- Layout optimization

**Performance:**
- Zero-cost abstractions
- SIMD optimizations
- Cache-friendly data structures
- Inlining control
- Memory layout optimization

**Safety Guidelines:**
- Minimize unsafe code
- Document safety preconditions
- Provide safe abstractions
- Test thoroughly
- Use verification tools
- Follow borrowing rules

**Best Practices:**
- Isolate unsafe code
- Use RAII for resource management
- Validate preconditions
- Provide safe wrappers
- Use compiler attributes
- Review code carefully

**Common Pitfalls:**
- Dereferencing invalid pointers
- Data races
- Use after free
- Buffer overflows
- Misaligned access
- Lifetime violations
- Type punning issues
- Aliasing violations

**Tools:**
- Clippy for linting
- Miri for UB detection
- Valgrind for memory errors
- ASan/TSan sanitizers
- Address sanitizer
- Thread sanitizer

**Guidelines:**
- Use unsafe only when necessary
- Understand undefined behavior
- Follow safety guidelines
- Use safe alternatives when possible
- Test unsafe code thoroughly
- Review unsafe code carefully

Unsafe Rust provides the power of C/C++ with the safety guarantees of Rust when used correctly. It enables low-level programming, performance optimizations, and interoperability while maintaining overall safety through careful design and testing.
