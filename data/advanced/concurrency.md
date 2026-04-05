# Rust Concurrency

## Concurrency Fundamentals

### Threads
```rust
// Basic thread creation
use std::thread;
use std::time::Duration;

fn basic_threads() {
    let handle = thread::spawn(|| {
        println!("Hello from spawned thread!");
        thread::sleep(Duration::from_millis(1000));
        println!("Spawned thread finished!");
    });
    
    println!("Main thread continues...");
    
    handle.join().unwrap();
    println!("Main thread finished!");
}

// Thread with parameters
fn thread_with_parameters() {
    let data = vec![1, 2, 3, 4, 5];
    
    let handle = thread::spawn(move || {
        println!("Processing data: {:?}", data);
        let sum: i32 = data.iter().sum();
        println!("Sum: {}", sum);
    });
    
    handle.join().unwrap();
}

// Multiple threads
fn multiple_threads() {
    let mut handles = vec![];
    
    for i in 0..5 {
        let handle = thread::spawn(move || {
            println!("Thread {} starting", i);
            thread::sleep(Duration::from_millis(i * 100));
            println!("Thread {} finished", i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
}

// Thread panic handling
fn thread_panic_handling() {
    let handle = thread::spawn(|| {
        panic!("Something went wrong in thread!");
    });
    
    match handle.join() {
        Ok(_) => println!("Thread completed successfully"),
        Err(e) => println!("Thread panicked: {:?}", e),
    }
    
    let handle = thread::spawn(|| {
        println!("This thread will complete normally");
    });
    
    match handle.join() {
        Ok(_) => println!("Thread completed successfully"),
        Err(e) => println!("Thread panicked: {:?}", e),
    }
}
```

### Scoped Threads
```rust
// Scoped threads (requires crossbeam)
fn scoped_threads() {
    /*
    use crossbeam::thread;
    
    let data = vec![1, 2, 3, 4, 5];
    
    thread::scope(|s| {
        for i in 0..3 {
            s.spawn(|| {
                println!("Scoped thread {} processing: {:?}", i, data);
            });
        }
    }).unwrap();
    
    println!("All scoped threads completed");
    */
    
    // Alternative implementation without external crate
    let data = vec![1, 2, 3, 4, 5];
    let data_clone = data.clone();
    
    let handle = thread::spawn(move || {
        println!("Processing data in scoped-like thread: {:?}", data_clone);
    });
    
    handle.join().unwrap();
    println!("Scoped-like thread completed");
}

// Thread builders
fn thread_builders() {
    use std::thread::Builder;
    
    let handle = Builder::new()
        .name("worker-thread".to_string())
        .stack_size(1024 * 1024) // 1MB stack
        .spawn(|| {
            println!("Thread with custom name and stack size");
            thread::sleep(Duration::from_millis(500));
        })
        .unwrap();
    
    handle.join().unwrap();
}

// Thread local storage
fn thread_local_storage() {
    use std::cell::RefCell;
    
    thread_local! {
        static THREAD_ID: RefCell<usize> = RefCell::new(0);
    }
    
    let handles: Vec<_> = (0..5).map(|i| {
        thread::spawn(move || {
            THREAD_ID.with(|id| {
                *id.borrow_mut() = i;
                println!("Thread {} ID: {}", i, *id.borrow());
            });
        })
    }).collect();
    
    for handle in handles {
        handle.join().unwrap();
    }
}
```

### Thread Communication

### Channels
```rust
// Basic channel communication
use std::sync::mpsc;
use std::thread;

fn basic_channels() {
    let (sender, receiver) = mpsc::channel();
    
    thread::spawn(move || {
        for i in 0..5 {
            sender.send(i).unwrap();
            thread::sleep(Duration::from_millis(100));
        }
    });
    
    for received in receiver {
        println!("Received: {}", received);
    }
}

// Multiple producers
fn multiple_producers() {
    let (sender, receiver) = mpsc::channel();
    
    for i in 0..3 {
        let sender = sender.clone();
        thread::spawn(move || {
            for j in 0..3 {
                let value = i * 10 + j;
                sender.send(value).unwrap();
                thread::sleep(Duration::from_millis(100));
            }
        });
    }
    
    drop(sender); // Close the channel
    
    for received in receiver {
        println!("Received: {}", received);
    }
}

// Synchronous channels (oneshot)
fn oneshot_channels() {
    use std::sync::oneshot;
    
    let (sender, receiver) = oneshot::channel();
    
    thread::spawn(move || {
        thread::sleep(Duration::from_millis(1000));
        sender.send("Hello from thread!").unwrap();
    });
    
    match receiver.recv() {
        Ok(message) => println!("Received: {}", message),
        Err(e) => println!("Error: {}", e),
    }
}

// Bounded channels
fn bounded_channels() {
    use std::sync::mpsc;
    
    let (sender, receiver) = mpsc::bounded_channel(2);
    
    let sender_clone = sender.clone();
    
    // Producer 1
    thread::spawn(move || {
        for i in 0..3 {
            println!("Producer 1 sending: {}", i);
            match sender.send(i) {
                Ok(_) => println!("Producer 1 sent: {}", i),
                Err(e) => println!("Producer 1 error: {}", e),
            }
            thread::sleep(Duration::from_millis(200));
        }
    });
    
    // Producer 2
    thread::spawn(move || {
        for i in 10..13 {
            println!("Producer 2 sending: {}", i);
            match sender_clone.send(i) {
                Ok(_) => println!("Producer 2 sent: {}", i),
                Err(e) => println!("Producer 2 error: {}", e),
            }
            thread::sleep(Duration::from_millis(150));
        }
    });
    
    // Consumer
    thread::spawn(move || {
        for received in receiver {
            println!("Consumer received: {}", received);
            thread::sleep(Duration::from_millis(300));
        }
    });
    
    thread::sleep(Duration::from_millis(2000));
}
```

### Shared State
```rust
// Shared state with Arc and Mutex
use std::sync::{Arc, Mutex};
use std::thread;

fn shared_state() {
    let counter = Arc::new(Mutex::new(0));
    let mut handles = vec![];
    
    for _ in 0..10 {
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

// Shared state with RwLock
use std::sync::RwLock;

fn rwlock_example() {
    let data = Arc::new(RwLock::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    // Readers
    for i in 0..3 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let data = data_clone.read().unwrap();
            println!("Reader {} sees: {:?}", i, data);
            thread::sleep(Duration::from_millis(100));
        });
        handles.push(handle);
    }
    
    // Writer
    let data_clone = Arc::clone(&data);
    let writer_handle = thread::spawn(move || {
        thread::sleep(Duration::from_millis(50));
        let mut data = data_clone.write().unwrap();
        data.push(6);
        data.push(7);
        println!("Writer modified data: {:?}", data);
    });
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    writer_handle.join().unwrap();
    
    println!("Final data: {:?}", *data.read().unwrap());
}

// Atomic operations
use std::sync::atomic::{AtomicUsize, Ordering};

fn atomic_operations() {
    let counter = Arc::new(AtomicUsize::new(0));
    let mut handles = vec![];
    
    for _ in 0..10 {
        let counter_clone = Arc::clone(&counter);
        let handle = thread::spawn(move || {
            for _ in 0..1000 {
                counter_clone.fetch_add(1, Ordering::Relaxed);
            }
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Final atomic counter: {}", counter.load(Ordering::Relaxed));
}
```

## Synchronization Primitives

### Mutex
```rust
// Basic Mutex usage
use std::sync::Mutex;
use std::thread;

fn basic_mutex() {
    let data = Mutex::new(vec![1, 2, 3]);
    
    {
        let mut data = data.lock().unwrap();
        data.push(4);
        data.push(5);
    } // Mutex is automatically unlocked here
    
    let data = data.lock().unwrap();
    println!("Data: {:?}", data);
}

// Mutex poisoning
fn mutex_poisoning() {
    let data = Mutex::new(vec![1, 2, 3]);
    
    let handle = thread::spawn(move || {
        let mut data = data.lock().unwrap();
        data.push(4);
        panic!("Something went wrong!");
    });
    
    match handle.join() {
        Ok(_) => println!("Thread completed"),
        Err(e) => println!("Thread panicked: {:?}", e),
    }
    
    // Mutex is now poisoned
    match data.lock() {
        Ok(data) => println!("Data: {:?}", data),
        Err(e) => println!("Mutex is poisoned: {:?}", e),
    }
    
    // Recover from poisoning
    match data.lock() {
        Ok(data) => println!("Data: {:?}", data),
        Err(poisoned) => {
            let data = poisoned.into_inner();
            println!("Recovered data: {:?}", data);
        }
    }
}

// Mutex with multiple threads
fn mutex_multiple_threads() {
    let counter = Mutex::new(0);
    let mut handles = vec![];
    
    for i in 0..10 {
        let counter_clone = counter.clone();
        let handle = thread::spawn(move || {
            for _ in 0..100 {
                let mut num = counter_clone.lock().unwrap();
                *num += 1;
                thread::sleep(Duration::from_millis(1));
            }
            println!("Thread {} finished", i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Final counter: {}", *counter.lock().unwrap());
}
```

### RwLock
```rust
// Basic RwLock usage
use std::sync::RwLock;
use std::thread;

fn basic_rwlock() {
    let data = RwLock::new(vec![1, 2, 3]);
    
    // Multiple readers
    let mut handles = vec![];
    for i in 0..5 {
        let data_clone = data.clone();
        let handle = thread::spawn(move || {
            let data = data_clone.read().unwrap();
            println!("Reader {} sees: {:?}", i, data);
            thread::sleep(Duration::from_millis(100));
        });
        handles.push(handle);
    }
    
    // Writer
    let data_clone = data.clone();
    let writer_handle = thread::spawn(move || {
        thread::sleep(Duration::from_millis(50));
        let mut data = data_clone.write().unwrap();
        data.push(4);
        data.push(5);
        println!("Writer modified data: {:?}", data);
    });
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    writer_handle.join().unwrap();
    
    println!("Final data: {:?}", *data.read().unwrap());
}

// RwLock with concurrent readers and writers
fn concurrent_rwlock() {
    let data = RwLock::new(vec![1, 2, 3]);
    let mut handles = vec![];
    
    // Readers
    for i in 0..3 {
        let data_clone = data.clone();
        let handle = thread::spawn(move || {
            for j in 0..5 {
                let data = data_clone.read().unwrap();
                println!("Reader {} iteration {}: {:?}", i, j, data);
                thread::sleep(Duration::from_millis(50));
            }
        });
        handles.push(handle);
    }
    
    // Writers
    for i in 0..2 {
        let data_clone = data.clone();
        let handle = thread::spawn(move || {
            thread::sleep(Duration::from_millis(100));
            let mut data = data_clone.write().unwrap();
            data.push(4 + i);
            println!("Writer {} added: {}", i, 4 + i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Final data: {:?}", *data.read().unwrap());
}
```

### Condvar
```rust
// Conditional variables
use std::sync::{Arc, Condvar, Mutex};
use std::thread;

fn condvar_basic() {
    let pair = Arc::new((Mutex::new(false), Condvar::new()));
    let pair_clone = pair.clone();
    
    thread::spawn(move || {
        let (ref lock, ref cvar) = *pair_clone;
        thread::sleep(Duration::from_millis(1000));
        
        let mut started = lock.lock().unwrap();
        *started = true;
        cvar.notify_one();
    });
    
    let (ref lock, ref cvar) = *pair;
    let mut started = lock.lock().unwrap();
    
    while !*started {
        started = cvar.wait(started).unwrap();
    }
    
    println!("Condition met!");
}

// Producer-consumer with Condvar
fn producer_consumer_condvar() {
    let queue = Arc::new((Mutex::new(Vec::<i32>::new()), Condvar::new()));
    let queue_clone = queue.clone();
    
    // Producer
    let producer = thread::spawn(move || {
        for i in 0..10 {
            {
                let (ref lock, ref cvar) = *queue_clone;
                let mut queue = lock.lock().unwrap();
                
                while queue.len() >= 5 {
                    queue = cvar.wait(queue).unwrap();
                }
                
                queue.push(i);
                println!("Produced: {}", i);
                cvar.notify_all();
            }
            thread::sleep(Duration::from_millis(50));
        }
    });
    
    // Consumer
    let consumer = thread::spawn(move || {
        for _ in 0..10 {
            {
                let (ref lock, ref cvar) = *queue;
                let mut queue = lock.lock().unwrap();
                
                while queue.is_empty() {
                    queue = cvar.wait(queue).unwrap();
                }
                
                let item = queue.remove(0);
                println!("Consumed: {}", item);
                cvar.notify_all();
            }
            thread::sleep(Duration::from_millis(100));
        }
    });
    
    producer.join().unwrap();
    consumer.join().unwrap();
}
```

## Async/Await

### Basic Async/Await
```rust
// Basic async function (requires async-std)
/*
use async_std::task;
use async_std::future;

async fn basic_async() {
    println!("Starting async function");
    future::sleep(Duration::from_millis(1000)).await;
    println!("Async function completed");
}

async fn async_with_return() -> String {
    println!("Starting async function with return");
    future::sleep(Duration::from_millis(500)).await;
    "Async result".to_string()
}

fn run_async_examples() {
    task::block_on(async {
        basic_async().await;
        
        let result = async_with_return().await;
        println!("Result: {}", result);
    });
}
*/

// Async with error handling
/*
async fn async_with_error() -> Result<String, String> {
    println!("Starting async function with error");
    
    // Simulate potential error
    let success = true;
    
    if success {
        future::sleep(Duration::from_millis(500)).await;
        Ok("Success".to_string())
    } else {
        Err("Error occurred".to_string())
    }
}

fn run_async_with_error() {
    task::block_on(async {
        match async_with_error().await {
            Ok(result) => println!("Success: {}", result),
            Err(e) => println!("Error: {}", e),
        }
    });
}
*/

// Sequential async operations
/*
async fn sequential_async() {
    println!("Starting sequential async operations");
    
    let result1 = async_operation("Operation 1").await;
    println!("Result 1: {}", result1);
    
    let result2 = async_operation("Operation 2").await;
    println!("Result 2: {}", result2);
    
    let result3 = async_operation("Operation 3").await;
    println!("Result 3: {}", result3);
}

async fn async_operation(name: &str) -> String {
    println!("Starting {}", name);
    future::sleep(Duration::from_millis(500)).await;
    format!("{} completed", name)
}

fn run_sequential_async() {
    task::block_on(async {
        sequential_async().await;
    });
}
*/

// Concurrent async operations
/*
async fn concurrent_async() {
    println!("Starting concurrent async operations");
    
    let op1 = async_operation("Operation 1");
    let op2 = async_operation("Operation 2");
    let op3 = async_operation("Operation 3");
    
    let (result1, result2, result3) = future::join!(op1, op2, op3);
    
    println!("Concurrent results:");
    println!("  Result 1: {}", result1);
    println!("  Result 2: {}", result2);
    println!("  Result 3: {}", result3);
}

fn run_concurrent_async() {
    task::block_on(async {
        concurrent_async().await;
    });
}
*/
```

### Futures and Streams
```rust
// Working with futures
/*
use futures::future;
use futures::stream::{self, StreamExt};

fn basic_futures() {
    task::block_on(async {
        let future1 = async {
            println!("Future 1 starting");
            future::sleep(Duration::from_millis(500)).await;
            "Future 1 result"
        };
        
        let future2 = async {
            println!("Future 2 starting");
            future::sleep(Duration::from_millis(300)).await;
            "Future 2 result"
        };
        
        let (result1, result2) = future::join!(future1, future2);
        
        println!("Future results: {}, {}", result1, result2);
    });
}

// Working with streams
fn basic_streams() {
    task::block_on(async {
        let stream = stream::iter(vec![1, 2, 3, 4, 5]);
        
        stream.for_each(|value| {
            println!("Stream value: {}", value);
        }).await;
    });
}

// Stream transformation
fn stream_transformation() {
    task::block_on(async {
        let stream = stream::iter(vec![1, 2, 3, 4, 5])
            .map(|x| x * 2)
            .filter(|&x| x > 4)
            .take(2);
        
        stream.for_each(|value| {
            println!("Transformed value: {}", value);
        }).await;
    });
}
*/
```

## Thread Pools

### Simple Thread Pool
```rust
// Simple thread pool implementation
use std::sync::{Arc, Mutex, mpsc};
use std::thread;

struct ThreadPool {
    workers: Vec<Worker>,
    sender: mpsc::Sender<Job>,
}

type Job = Box<dyn FnOnce() + Send + 'static>;

struct Worker {
    id: usize,
    handle: thread::JoinHandle<()>,
}

impl Worker {
    fn new(id: usize, receiver: Arc<Mutex<mpsc::Receiver<Job>>>) -> Self {
        let handle = thread::spawn(move || {
            loop {
                let job = receiver.lock().unwrap().recv().unwrap();
                
                println!("Worker {} got a job; executing.", id);
                job();
            }
        });
        
        Worker { id, handle }
    }

impl ThreadPool {
    /// Create a new ThreadPool.
    ///
    /// The size is the number of threads in the pool.
    pub fn new(size: usize) -> ThreadPool {
        assert!(size > 0);
        
        let (sender, receiver) = mpsc::channel();
        let receiver = Arc::new(Mutex::new(receiver));
        
        let mut workers = Vec::with_capacity(size);
        
        for id in 0..size {
            workers.push(Worker::new(id, receiver.clone()));
        }
        
        ThreadPool { workers, sender }
    }
    
    /// Execute a job in the thread pool
    pub fn execute<F>(&self, f: F)
    where
        F: FnOnce() + Send + 'static,
    {
        let job = Box::new(f);
        self.sender.send(job).unwrap();
    }
}

fn thread_pool_example() {
    let pool = ThreadPool::new(4);
    
    for i in 0..8 {
        pool.execute(move || {
            println!("Task {} executing", i);
            thread::sleep(Duration::from_millis(500));
            println!("Task {} completed", i);
        });
    }
    
    thread::sleep(Duration::from_millis(2000));
    println!("All tasks completed");
}
```

### Work Stealing Thread Pool
```rust
// Work-stealing thread pool implementation
/*
use std::sync::{Arc, Mutex, mpsc};
use std::thread;
use std::collections::VecDeque;

struct WorkStealingPool {
    workers: Vec<Worker>,
    work_queues: Vec<Arc<Mutex<VecDeque<Job>>>,
    stealers: Vec<mpsc::Sender<Job>>,
}

impl WorkStealingPool {
    /// Create a new WorkStealingPool.
    pub fn new(size: usize) -> WorkStealingPool {
        assert!(size > 0);
        
        let (sender, receiver) = mpsc::channel();
        let receiver = Arc::new(Mutex::new(receiver));
        
        let mut workers = Vec::with_capacity(size);
        let mut work_queues = Vec::with_capacity(size);
        let mut stealers = Vec::with_capacity(size);
        
        for id in 0..size {
            let (stealer_sender, stealer_receiver) = mpsc::channel();
            let work_queue = Arc::new(Mutex::new(VecDeque::new()));
            
            workers.push(Worker::new(id, work_queue.clone(), stealer_receiver));
            work_queues.push(work_queue);
            stealers.push(stealer_sender);
        }
        
        WorkStealingPool {
            workers,
            work_queues,
            stealers,
        }
    }
    
    /// Execute a job in the thread pool
    pub fn execute<F>(&self, f: F)
    where
        F: FnOnce() + Send + 'static,
    {
        let job = Box::new(f);
        
        // Send to the first worker's queue
        if let Some(first_queue) = self.work_queues.first() {
            first_queue.lock().unwrap().push_back(job);
        }
    }
    
    /// Try to steal work from other workers
    fn try_steal_work(&self, worker_id: usize) -> Option<Job> {
        for (i, queue) in self.work_queues.iter().enumerate() {
            if i != worker_id {
                if let Ok(job) = queue.lock().unwrap().pop_front() {
                    return Some(job);
                }
            }
        }
        None
    }
}

struct Worker {
    id: usize,
    handle: thread::JoinHandle<()>,
}

impl Worker {
    fn new(
        id: usize,
        work_queue: Arc<Mutex<VecDeque<Job>>>,
        stealer_receiver: mpsc::Receiver<Job>,
        other_queues: Vec<Arc<Mutex<VecDeque<Job>>>>,
    ) -> Self {
        let handle = thread::spawn(move || {
            loop {
                // Try to get work from own queue
                let job = work_queue.lock().unwrap().pop_front()
                    .or_else(|| {
                        // Try to steal from other queues
                        for (i, queue) in other_queues.iter().enumerate() {
                            if i != id {
                                if let Ok(job) = queue.lock().unwrap().pop_front() {
                                    return Some(job);
                                }
                            }
                        }
                        None
                    })
                    .or_else(|| {
                        // Try to get work from stealer
                        stealer_receiver.try_recv().ok()
                    });
                
                match job {
                    Some(job) => {
                        println!("Worker {} executing job", id);
                        job();
                    }
                    None => {
                        // No work available, wait a bit
                        thread::sleep(Duration::from_millis(100));
                    }
                }
            }
        });
        
        Worker { id, handle }
    }

fn work_stealing_pool_example() {
    let pool = WorkStealingPool::new(4);
    
    for i in 0..20 {
        pool.execute(move || {
            println!("Task {} executing", i);
            thread::sleep(Duration::from_millis(100));
            println!("Task {} completed", i);
        });
    }
    
    thread::sleep(Duration::from_millis(2000));
    println!("All tasks completed");
}
*/
```

## Actor Model

### Basic Actor
```rust
// Basic actor implementation
use std::sync::mpsc;
use std::thread;

struct Actor {
    sender: mpsc::Sender<Message>,
}

enum Message {
    GetData(mpsc::Sender<String>),
    SetData(String),
    Stop,
}

impl Actor {
    pub fn new() -> Self {
        let (sender, receiver) = mpsc::channel();
        
        thread::spawn(move || {
            let mut data = String::new();
            
            while let Ok(message) = receiver.recv() {
                match message {
                    Message::GetData(sender) => {
                        println!("Actor sending data: {}", data);
                        sender.send(data).unwrap();
                    }
                    Message::SetData(new_data) => {
                        println!("Actor setting data: {}", new_data);
                        data = new_data;
                    }
                    Message::Stop => {
                        println!("Actor stopping");
                        break;
                    }
                }
            }
        });
        
        Actor { sender }
    }
    
    pub fn send_message(&self, message: Message) {
        self.sender.send(message).unwrap();
    }
}

fn actor_example() {
    let actor = Actor::new();
    
    // Set initial data
    actor.send_message(Message::SetData("Initial data".to_string()));
    
    // Get data
    let (sender, receiver) = mpsc::channel();
    actor.send_message(Message::GetData(sender));
    
    let data = receiver.recv().unwrap();
    println!("Received data: {}", data);
    
    // Stop actor
    actor.send_message(Message::Stop);
    
    thread::sleep(Duration::from_millis(100));
}
```

### Request-Response Actor
```rust
// Request-Response actor
use std::sync::mpsc;
use std::thread;
use std::collections::HashMap;

enum Request {
    Get(String, mpsc::Sender<String>),
    Set(String, String),
    Delete(String, mpsc::Sender<bool>),
}

struct DatabaseActor {
    sender: mpsc::Sender<Request>,
}

impl DatabaseActor {
    pub fn new() -> Self {
        let (sender, receiver) = mpsc::channel();
        
        thread::spawn(move || {
            let mut database = HashMap::new();
            
            while let Ok(request) = receiver.recv() {
                match request {
                    Request::Get(key, sender) => {
                        let value = database.get(&key).cloned().unwrap_or_else(|| "Not found".to_string());
                        sender.send(value).unwrap();
                    }
                    Request::Set(key, value) => {
                        database.insert(key, value);
                    }
                    Request::Delete(key, sender) => {
                        let deleted = database.remove(&key).is_some();
                        sender.send(deleted).unwrap();
                    }
                }
            }
        });
        
        DatabaseActor { sender }
    }
    
    pub fn send_request(&self, request: Request) {
        self.sender.send(request).unwrap();
    }
}

fn database_actor_example() {
    let db = DatabaseActor::new();
    
    // Set values
    db.send_request(Request::Set("key1".to_string(), "value1".to_string()));
    db.send_request(Request::Set("key2".to_string(), "value2".to_string()));
    
    // Get values
    let (sender1, receiver1) = mpsc::channel();
    db.send_request(Request::Get("key1".to_string(), sender1));
    let value1 = receiver1.recv().unwrap();
    println!("Got value1: {}", value1);
    
    let (sender2, receiver2) = mpsc::channel();
    db.send_request(Request::Get("key2".to_string(), sender2));
    let value2 = receiver2.recv().unwrap();
    println!("Got value2: {}", value2);
    
    // Get non-existent key
    let (sender3, receiver3) = mpsc::channel();
    db.send_request(Request::Get("nonexistent".to_string(), sender3));
    let value3 = receiver3.recv().unwrap();
    println!("Got nonexistent: {}", value3);
    
    // Delete key
    let (sender4, receiver4) = mpsc::channel();
    db.send_request(Request::Delete("key1".to_string(), sender4));
    let deleted = receiver4.recv().unwrap();
    println!("Deleted key1: {}", deleted);
    
    // Try to get deleted key
    let (sender5, receiver5) = mpsc::channel();
    db.send_request(Request::Get("key1".to_string(), sender5));
    let value5 = receiver5.recv().unwrap();
    println!("Got deleted key1: {}", value5);
}
```

## Best Practices

### Concurrency Best Practices
```rust
// 1. Use Arc for shared ownership
fn use_arc_for_sharing() {
    use std::sync::Arc;
    
    let data = Arc::new(vec![1, 2, 3, 4, 5]);
    let data_clone = Arc::clone(&data);
    
    thread::spawn(move || {
        println!("Thread sees: {:?}", data_clone);
    });
    
    println!("Main thread sees: {:?}", data);
}

// 2. Use Mutex for exclusive access
fn use_mutex_for_exclusive() {
    use std::sync::{Arc, Mutex};
    
    let counter = Arc::new(Mutex::new(0));
    let mut handles = vec![];
    
    for _ in 0..10 {
        let counter_clone = Arc::clone(&counter);
        let handle = thread::spawn(move || {
            let mut num = counter_clone.lock().unwrap();
            *num += 1;
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Final counter: {}", *counter.lock().unwrap());
}

// 3. Use RwLock for read-heavy workloads
fn use_rwlock_for_read_heavy() {
    use std::sync::{Arc, RwLock};
    
    let data = Arc::new(RwLock::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    // Multiple readers
    for i in 0..5 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let data = data_clone.read().unwrap();
            println!("Reader {} sees: {:?}", i, data);
            thread::sleep(Duration::from_millis(100));
        });
        handles.push(handle);
    }
    
    // Single writer
    let data_clone = Arc::clone(&data);
    let writer_handle = thread::spawn(move || {
        thread::sleep(Duration::from_millis(50));
        let mut data = data_clone.write().unwrap();
        data.push(6);
        println!("Writer modified data: {:?}", data);
    });
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    writer_handle.join().unwrap();
    
    println!("Final data: {:?}", *data.read().unwrap());
}

// 4. Use channels for communication
fn use_channels_for_communication() {
    use std::sync::mpsc;
    
    let (sender, receiver) = mpsc::channel();
    
    thread::spawn(move || {
        for i in 0..10 {
            sender.send(i).unwrap();
            thread::sleep(Duration::from_millis(100));
        }
    });
    
    for received in receiver {
        println!("Received: {}", received);
    }
}

// 5. Use thread pools for work distribution
fn use_thread_pools() {
    let pool = ThreadPool::new(4);
    
    for i in 0..20 {
        pool.execute(move || {
            println!("Task {} executing", i);
            thread::sleep(Duration::from_millis(50));
        });
    }
    
    thread::sleep(Duration::from_millis(1000));
}

// 6. Avoid deadlocks
fn avoid_deadlocks() {
    use std::sync::{Arc, Mutex};
    
    // Bad: potential deadlock
    let mutex1 = Arc::new(Mutex::new(0));
    let mutex2 = Arc::new(Mutex::new(0));
    
    let mutex1_clone = Arc::clone(&mutex1);
    let mutex2_clone = Arc::clone(&mutex2);
    
    let handle1 = thread::spawn(move || {
        let _lock1 = mutex1_clone.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock2 = mutex2_clone.lock().unwrap();
    });
    
    let handle2 = thread::spawn(move || {
        let _lock2 = mutex2.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock1 = mutex1_clone.lock().unwrap();
    });
    
    // Good: consistent locking order
    let mutex3 = Arc::new(Mutex::new(0));
    let mutex4 = Arc::new(Mutex::new(0));
    
    let mutex3_clone = Arc::clone(&mutex3);
    let mutex4_clone = Arc::clone(&mutex4);
    
    let handle3 = thread::spawn(move || {
        let _lock3 = mutex3_clone.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock4 = mutex4_clone.lock().unwrap();
    });
    
    let handle4 = thread::spawn(move || {
        let _lock3 = mutex3_clone.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock4 = mutex4_clone.lock().unwrap();
    });
    
    handle1.join().unwrap();
    handle2.join().unwrap();
    handle3.join().unwrap();
    handle4.join().unwrap();
}

// 7. Use atomic operations for simple counters
fn use_atomic_operations() {
    use std::sync::atomic::{AtomicUsize, Ordering};
    
    let counter = Arc::new(AtomicUsize::new(0));
    let mut handles = vec![];
    
    for _ in 0..10 {
        let counter_clone = Arc::clone(&counter);
        let handle = thread::spawn(move || {
            for _ in 0..1000 {
                counter_clone.fetch_add(1, Ordering::Relaxed);
            }
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Final counter: {}", counter.load(Ordering::Relaxed));
}

// 8. Handle thread panic gracefully
fn handle_thread_panic() {
    use std::panic;
    
    let result = thread::spawn(|| {
        panic!("Something went wrong!");
    }).join();
    
    match result {
        Ok(_) => println!("Thread completed successfully"),
        Err(e) => println!("Thread panicked: {:?}", e),
    }
    
    // Set panic hook for global panic handling
    panic::set_hook(Box::new(|info| {
        println!("Panic occurred: {}", info);
    }));
    
    // This would trigger the panic hook
    // panic!("Global panic");
}
```

### Performance Considerations
```rust
// 1. Minimize lock contention
fn minimize_lock_contention() {
    use std::sync::{Arc, Mutex};
    use std::time::Instant;
    
    let data = Arc::new(Mutex::new(Vec::new()));
    let mut handles = vec![];
    
    let start = Instant::now();
    
    for i in 0..1000 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let mut vec = data_clone.lock().unwrap();
            vec.push(i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    let duration = start.elapsed();
    println!("High contention: {:?}", duration);
    
    // Better: use sharding
    let shards: Vec<_> = (0..4).map(|_| Arc::new(Mutex::new(Vec::new()))).collect();
    let mut handles = vec![];
    
    let start = Instant::now();
    
    for i in 0..1000 {
        let shard = Arc::clone(&shards[i % 4]);
        let handle = thread::spawn(move || {
            let mut vec = shard.lock().unwrap();
            vec.push(i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    let duration = start.elapsed();
    println!("Low contention (sharding): {:?}", duration);
}

// 2. Use appropriate synchronization primitives
fn appropriate_synchronization() {
    use std::sync::{Arc, Mutex, RwLock};
    
    // For exclusive access: Mutex
    let counter = Arc::new(Mutex::new(0));
    
    // For read-heavy workloads: RwLock
    let data = Arc::new(RwLock::new(vec![1, 2, 3]));
    
    // For simple counters: AtomicUsize
    use std::sync::atomic::{AtomicUsize, Ordering};
    let atomic_counter = Arc::new(AtomicUsize::new(0));
    
    // Choose based on use case
    let _ = counter.lock().unwrap();
    let _ = data.read().unwrap();
    let _ = atomic_counter.load(Ordering::Relaxed);
}

// 3. Batch operations
fn batch_operations() {
    use std::sync::{Arc, Mutex};
    
    let data = Arc::new(Mutex::new(Vec::new()));
    let mut handles = vec![];
    
    for i in 0..10 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            // Bad: lock for each operation
            for j in 0..100 {
                let mut vec = data_clone.lock().unwrap();
                vec.push(i * 100 + j);
            }
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Individual operations: {}", data.lock().unwrap().len());
    
    // Better: batch operations
    let data = Arc::new(Mutex::new(Vec::new()));
    let data_clone = Arc::clone(&data);
    
    let handle = thread::spawn(move || {
        let mut batch = Vec::new();
        
        for i in 0..1000 {
            batch.push(i);
        }
        
        let mut vec = data_clone.lock().unwrap();
        vec.extend(batch);
    });
    
    handle.join().unwrap();
    println!("Batch operations: {}", data.lock().unwrap().len());
}

// 4. Use lock-free data structures when possible
fn lock_free_structures() {
    use std::sync::Arc;
    use std::sync::atomic::{AtomicUsize, Ordering};
    
    struct LockFreeCounter {
        value: AtomicUsize,
    }
    
    impl LockFreeCounter {
        fn new() -> Self {
            LockFreeCounter {
                value: AtomicUsize::new(0),
            }
        }
        
        fn increment(&self) {
            self.value.fetch_add(1, Ordering::Relaxed);
        }
        
        fn get(&self) -> usize {
            self.value.load(Ordering::Relaxed);
        }
    }
    
    let counter = Arc::new(LockFreeCounter::new());
    let mut handles = vec![];
    
    for _ in 0..10 {
        let counter_clone = Arc::clone(&counter);
        let handle = thread::spawn(move || {
            for _ in 0..1000 {
                counter_clone.increment();
            }
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Lock-free counter: {}", counter.get());
}

// 5. Use thread pools for CPU-bound tasks
fn thread_pools_for_cpu_bound() {
    let pool = ThreadPool::new(num_cpus::get());
    
    // CPU-bound tasks
    for i in 0..20 {
        pool.execute(move || {
            let mut result = 1;
            for j in 1..1000 {
                result *= j;
            }
            println!("Task {} result: {}", i, result);
        });
    }
    
    thread::sleep(Duration::from_millis(1000));
}

// 6. Use async/await for I/O-bound tasks
/*
use async_std::task;

async fn async_for_io_bound() {
    // Simulate I/O operations
    let future1 = async_std::fs::read_to_string("file1.txt");
    let future2 = async_std::fs::read_to_string("file2.txt");
    let future3 = async_std::fs::read_to_string("file3.txt");
    
    let (content1, content2, content3) = future::join!(future1, future2, future3);
    
    println!("Read {} bytes from file1", content1.len());
    println!("Read {} bytes from file2", content2.len());
    println!("Read {} bytes from file3", content3.len());
}

fn run_async_io_bound() {
    task::block_on(async_for_io_bound());
}
*/
```

## Common Pitfalls

### Common Concurrency Mistakes
```rust
// 1. Data races
fn data_races() {
    use std::sync::Arc;
    
    // Bad: data race
    let data = Arc::new(vec![1, 2, 3, 4, 5]);
    let mut handles = vec![];
    
    for i in 0..10 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            // This can cause data races if not synchronized
            data_clone.push(i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Data race result: {:?}", data);
    
    // Good: use synchronization
    use std::sync::Mutex;
    
    let safe_data = Arc::new(Mutex::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    for i in 0..10 {
        let safe_data_clone = Arc::clone(&safe_data);
        let handle = thread::spawn(move || {
            let mut vec = safe_data_clone.lock().unwrap();
            vec.push(i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Synchronized result: {:?}", safe_data.lock().unwrap());
}

// 2. Deadlocks
fn deadlocks() {
    use std::sync::{Arc, Mutex};
    
    let mutex1 = Arc::new(Mutex::new(0));
    let mutex2 = Arc::new(Mutex::new(0));
    
    // Bad: potential deadlock
    let mutex1_clone = Arc::clone(&mutex1);
    let mutex2_clone = Arc::clone(&mutex2);
    
    let handle1 = thread::spawn(move || {
        let _lock1 = mutex1_clone.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock2 = mutex2_clone.lock().unwrap(); // Potential deadlock
    });
    
    let handle2 = thread::spawn(move || {
        let _lock2 = mutex2_clone.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock1 = mutex1_clone.lock().unwrap(); // Potential deadlock
    });
    
    // Good: consistent locking order
    let mutex3 = Arc::new(Mutex::new(0));
    let mutex4 = Arc::new(Mutex::new(0));
    
    let mutex3_clone = Arc::clone(&mutex3);
    let mutex4_clone = Arc::clone(&mutex4);
    
    let handle3 = thread::spawn(move || {
        let _lock3 = mutex3_clone.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock4 = mutex4_clone.lock().unwrap();
    });
    
    let handle4 = thread::spawn(move || {
        let _lock3 = mutex3_clone.lock().unwrap();
        thread::sleep(Duration::from_millis(100));
        let _lock4 = mutex4_clone.lock().unwrap();
    });
    
    handle1.join().unwrap();
    handle2.join().unwrap();
    handle3.join().unwrap();
    handle4.join().unwrap();
}

// 3. Race conditions
fn race_conditions() {
    use std::sync::{Arc, Mutex};
    
    let flag = Arc::new(Mutex::new(false));
    let flag_clone = Arc::clone(&flag);
    
    let handle1 = thread::spawn(move || {
        thread::sleep(Duration::from_millis(100));
        let mut flag = flag_clone.lock().unwrap();
        *flag = true;
        println!("Thread 1 set flag to true");
    });
    
    let handle2 = thread::spawn(move || {
        thread::sleep(Duration::from_millis(50));
        let flag = flag_clone.lock().unwrap();
        if *flag {
            println!("Thread 2 sees flag as true");
        } else {
            println!("Thread 2 sees flag as false");
        }
    });
    
    handle1.join().unwrap();
    handle2.join().unwrap();
}

// 4. Not handling thread panics
fn not_handling_panics() {
    // Bad: not handling panics
    let handle = thread::spawn(|| {
        panic!("Something went wrong!");
    });
    
    // This would panic the whole program
    // handle.join().unwrap();
    
    // Good: handle panics
    let safe_handle = thread::spawn(|| {
        panic!("Something went wrong!");
    });
    
    match safe_handle.join() {
        Ok(_) => println!("Thread completed successfully"),
        Err(e) => println!("Thread panicked: {:?}", e),
    }
}

// 5. Over-synchronization
fn over_synchronization() {
    use std::sync::{Arc, Mutex};
    
    // Bad: over-synchronization
    let data = Arc::new(Mutex::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    for i in 0..10 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            // Lock held for too long
            let mut vec = data_clone.lock().unwrap();
            vec.push(i);
            thread::sleep(Duration::from_millis(100));
            // Lock is held during sleep
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    // Good: minimize lock time
    let data = Arc::new(Mutex::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    for i in 0..10 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            // Lock held for minimal time
            let mut vec = data_clone.lock().unwrap();
            vec.push(i);
            // Lock released immediately
            thread::sleep(Duration::from_millis(100));
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    println!("Over-synchronization result: {:?}", data.lock().unwrap());
}

// 6. Using wrong synchronization primitive
fn wrong_synchronization() {
    use std::sync::{Arc, Mutex, RwLock};
    
    // Bad: using Mutex for read-heavy workload
    let data = Arc::new(Mutex::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    for i in 0..10 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let data = data_clone.lock().unwrap();
            println!("Reader {} sees: {:?}", i, data);
            thread::sleep(Duration::from_millis(100));
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    // Good: using RwLock for read-heavy workload
    let data = Arc::new(RwLock::new(vec![1, 2, 3, 4, 5]));
    let mut handles = vec![];
    
    for i in 0..10 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let data = data_clone.read().unwrap();
            println!("Reader {} sees: {:?}", i, data);
            thread::sleep(Duration::from_millis(100));
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
}

// 7. Not considering thread safety in async code
/*
async fn not_thread_safe_async() {
    use std::sync::Arc;
    
    let counter = Arc::new(Mutex::new(0));
    
    // Bad: async function with shared mutable state
    async fn increment_counter(counter: Arc<Mutex<i32>>) {
        let mut value = counter.lock().unwrap();
        *value += 1;
    }
    
    // This can cause issues if called concurrently
    let handles: Vec<_> = (0..10).map(|_| {
        let counter_clone = Arc::clone(&counter);
        task::spawn(async move || {
            increment_counter(&counter_clone).await;
        })
    }).collect();
    
    for handle in handles {
        handle.await;
    }
    
    println!("Not thread-safe result: {}", counter.lock().unwrap());
}

// Good: thread-safe async operations
async fn thread_safe_async() {
    use std::sync::Arc;
    use std::sync::atomic::{AtomicI32, Ordering};
    
    let counter = Arc::new(AtomicI32::new(0));
    
    async fn increment_counter(counter: Arc<AtomicI32>) {
        counter.fetch_add(1, Ordering::Relaxed);
    }
    
    let handles: Vec<_> = (0..10).map(|_| {
        let counter_clone = Arc::clone(&counter);
        task::spawn(async move || {
            increment_counter(&counter_clone).await;
        })
    }).collect();
    
    for handle in handles {
        handle.await;
    }
    
    println!("Thread-safe result: {}", counter.load(Ordering::Relaxed));
}

fn run_async_examples() {
    task::block_on(async {
        not_thread_safe_async().await;
        thread_safe_async().await;
    });
}
*/

// 8. Creating too many threads
fn too_many_threads() {
    // Bad: creating too many threads
    let mut handles = vec![];
    
    for i in 0..10000 {
        let handle = thread::spawn(move || {
            thread::sleep(Duration::from_millis(100));
            println!("Thread {} completed", i);
        });
        handles.push(handle);
    }
    
    for handle in handles {
        handle.join().unwrap();
    }
    
    // Good: use thread pool for many tasks
    let pool = ThreadPool::new(num_cpus::get());
    
    for i in 0..10000 {
        pool.execute(move || {
            thread::sleep(Duration::from_millis(10));
            println!("Task {} completed", i);
        });
    }
    
    thread::sleep(Duration::from_millis(1000));
}
```

## Summary

Rust concurrency provides powerful and safe concurrent programming capabilities:

**Threading:**
- Basic thread creation and management
- Thread builders with custom configuration
- Thread local storage
- Panic handling in threads

**Synchronization:**
- Mutex for exclusive access
- RwLock for read-write access
- Condvar for condition variables
- Atomic operations for lock-free programming

**Communication:**
- Channels for message passing
- Actor model for message-oriented concurrency
- Request-response patterns
- Producer-consumer patterns

**Advanced Patterns:**
- Thread pools for work distribution
- Work-stealing pools for load balancing
- Async/await for asynchronous operations
- Futures and streams for async programming

**Performance:**
- Minimize lock contention
- Use appropriate synchronization primitives
- Batch operations when possible
- Lock-free data structures
- Thread pools for CPU-bound tasks
- Async/await for I/O-bound tasks

**Best Practices:**
- Use Arc for shared ownership
- Use Mutex for exclusive access
- Use RwLock for read-heavy workloads
- Use channels for communication
- Use thread pools for work distribution
- Avoid deadlocks with consistent locking order
- Handle thread panics gracefully
- Use atomic operations for simple counters

**Common Pitfalls:**
- Data races without synchronization
- Deadlocks from inconsistent locking order
- Race conditions in shared state
- Not handling thread panics
- Over-synchronization
- Wrong synchronization primitive choice
- Thread safety issues in async code
- Creating too many threads

**Safety Features:**
- Compile-time race condition prevention
- Send and Sync traits for thread safety
- Mutex poisoning detection
- Panic-safe error handling

**Guidelines:**
- Design for thread safety from the start
- Minimize shared mutable state
- Use appropriate synchronization primitives
- Consider performance implications
- Test concurrent code thoroughly
- Handle errors gracefully
- Monitor thread performance

Rust's concurrency model, combined with its ownership system and type safety, provides safe concurrent programming while maintaining high performance. The combination of threads, synchronization primitives, and async/await enables building robust concurrent applications with minimal runtime overhead.
