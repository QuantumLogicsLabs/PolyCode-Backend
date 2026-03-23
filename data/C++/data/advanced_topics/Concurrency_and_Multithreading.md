# Module 13: Concurrency and Multithreading

## Learning Objectives
- Understand the fundamentals of multithreading in C++
- Master std::thread creation and management
- Learn about thread synchronization primitives
- Understand mutexes, locks, and condition variables
- Master atomic operations and memory ordering
- Learn about async, futures, and promises
- Understand thread pools and parallel algorithms

## Introduction to Multithreading

Multithreading allows programs to execute multiple threads concurrently, improving performance on multi-core systems and enabling responsive applications.

### Basic Thread Creation
```cpp
#include <iostream>
#include <thread>
#include <chrono>

void helloFunction() {
    std::cout << "Hello from thread!" << std::endl;
}

void countFunction(int id, int n) {
    for (int i = 0; i < n; i++) {
        std::cout << "Thread " << id << ": " << i << std::endl;
        std::this_thread::sleep_for(std::chrono::milliseconds(100));
    }
}

int main() {
    std::cout << "Main thread started" << std::endl;
    
    // Create and start a thread
    std::thread t1(helloFunction);
    
    // Create thread with parameters
    std::thread t2(countFunction, 1, 5);
    std::thread t3(countFunction, 2, 5);
    
    // Wait for threads to complete
    t1.join();
    t2.join();
    t3.join();
    
    std::cout << "Main thread finished" << std::endl;
    return 0;
}
```

### Thread Management
```cpp
#include <iostream>
#include <thread>
#include <vector>
#include <functional>

class Worker {
private:
    std::string name;
    
public:
    Worker(const std::string& n) : name(n) {}
    
    void operator()() {
        std::cout << "Worker " << name << " is running" << std::endl;
        std::this_thread::sleep_for(std::chrono::seconds(1));
        std::cout << "Worker " << name << " finished" << std::endl;
    }
    
    void doWork(int iterations) {
        for (int i = 0; i < iterations; i++) {
            std::cout << name << " iteration " << i << std::endl;
            std::this_thread::sleep_for(std::chrono::milliseconds(200));
        }
    }
};

void demonstrateThreadManagement() {
    std::cout << "=== Thread Management ===" << std::endl;
    
    // Thread with function object
    Worker worker1("Alice");
    std::thread t1(std::ref(worker1));
    
    // Thread with member function
    Worker worker2("Bob");
    std::thread t2(&Worker::doWork, &worker2, 3);
    
    // Thread with lambda
    std::thread t3([]() {
        std::cout << "Lambda thread running" << std::endl;
        for (int i = 0; i < 3; i++) {
            std::cout << "Lambda: " << i << std::endl;
            std::this_thread::sleep_for(std::chrono::milliseconds(300));
        }
    });
    
    // Move thread
    std::thread t4 = std::move(t3);
    
    // Check if thread is joinable
    if (t1.joinable()) {
        std::cout << "Thread t1 is joinable" << std::endl;
        t1.join();
    }
    
    if (t2.joinable()) {
        t2.join();
    }
    
    if (t4.joinable()) {
        t4.join();
    }
    
    // Detached thread
    std::thread t5([]() {
        std::this_thread::sleep_for(std::chrono::seconds(1));
        std::cout << "Detached thread completed" << std::endl;
    });
    t5.detach();
    
    std::cout << "Main thread continuing..." << std::endl;
    std::this_thread::sleep_for(std::chrono::seconds(2));
}

int main() {
    demonstrateThreadManagement();
    return 0;
}
```

## Thread Synchronization

### Mutex and Lock Guards
```cpp
#include <iostream>
#include <thread>
#include <mutex>
#include <vector>

class Counter {
private:
    int count;
    std::mutex mtx;
    
public:
    Counter() : count(0) {}
    
    void increment() {
        std::lock_guard<std::mutex> lock(mtx);
        count++;
        std::cout << "Count incremented to: " << count << " by thread " 
                  << std::this_thread::get_id() << std::endl;
    }
    
    void decrement() {
        std::unique_lock<std::mutex> lock(mtx);
        count--;
        std::cout << "Count decremented to: " << count << " by thread " 
                  << std::this_thread::get_id() << std::endl;
        lock.unlock();  // Explicit unlock
        
        // Do some other work without holding the lock
        std::this_thread::sleep_for(std::chrono::milliseconds(10));
    }
    
    int getCount() {
        std::lock_guard<std::mutex> lock(mtx);
        return count;
    }
    
    bool tryIncrement() {
        if (mtx.try_lock()) {
            count++;
            std::cout << "Count incremented to: " << count << " (try_lock succeeded)" << std::endl;
            mtx.unlock();
            return true;
        }
        std::cout << "Failed to acquire lock" << std::endl;
        return false;
    }
};

void workerFunction(Counter& counter, int iterations) {
    for (int i = 0; i < iterations; i++) {
        counter.increment();
        std::this_thread::sleep_for(std::chrono::milliseconds(10));
    }
}

void demonstrateMutex() {
    std::cout << "=== Mutex Demonstration ===" << std::endl;
    
    Counter counter;
    std::vector<std::thread> threads;
    
    // Create multiple threads
    for (int i = 0; i < 5; i++) {
        threads.emplace_back(workerFunction, std::ref(counter), 3);
    }
    
    // Wait for all threads to complete
    for (auto& thread : threads) {
        thread.join();
    }
    
    std::cout << "Final count: " << counter.getCount() << std::endl;
}

// Deadlock example
class DeadlockDemo {
private:
    std::mutex mutex1;
    std::mutex mutex2;
    
public:
    void function1() {
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " trying to lock mutex1" << std::endl;
        std::lock_guard<std::mutex> lock1(mutex1);
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " locked mutex1" << std::endl;
        
        std::this_thread::sleep_for(std::chrono::milliseconds(100));
        
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " trying to lock mutex2" << std::endl;
        std::lock_guard<std::mutex> lock2(mutex2);
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " locked mutex2" << std::endl;
    }
    
    void function2() {
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " trying to lock mutex2" << std::endl;
        std::lock_guard<std::mutex> lock2(mutex2);
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " locked mutex2" << std::endl;
        
        std::this_thread::sleep_for(std::chrono::milliseconds(100));
        
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " trying to lock mutex1" << std::endl;
        std::lock_guard<std::mutex> lock1(mutex1);
        std::cout << "Thread " << std::this_thread::get_id() 
                  << " locked mutex1" << std::endl;
    }
    
    // Safe version using std::lock
    void safeFunction1() {
        std::lock(mutex1, mutex2);
        std::lock_guard<std::mutex> lock1(mutex1, std::adopt_lock);
        std::lock_guard<std::mutex> lock2(mutex2, std::adopt_lock);
        
        std::cout << "Safe function 1 executed" << std::endl;
    }
    
    void safeFunction2() {
        std::lock(mutex2, mutex1);
        std::lock_guard<std::mutex> lock2(mutex2, std::adopt_lock);
        std::lock_guard<std::mutex> lock1(mutex1, std::adopt_lock);
        
        std::cout << "Safe function 2 executed" << std::endl;
    }
};

void demonstrateDeadlock() {
    std::cout << "\n=== Deadlock Demonstration ===" << std::endl;
    
    DeadlockDemo demo;
    
    std::cout << "Demonstrating deadlock (this will hang)..." << std::endl;
    // Uncomment the following lines to see deadlock
    /*
    std::thread t1(&DeadlockDemo::function1, &demo);
    std::thread t2(&DeadlockDemo::function2, &demo);
    
    t1.join();
    t2.join();
    */
    
    std::cout << "Demonstrating safe locking..." << std::endl;
    std::thread t3(&DeadlockDemo::safeFunction1, &demo);
    std::thread t4(&DeadlockDemo::safeFunction2, &demo);
    
    t3.join();
    t4.join();
}

int main() {
    demonstrateMutex();
    demonstrateDeadlock();
    return 0;
}
```

### Condition Variables
```cpp
#include <iostream>
#include <thread>
#include <mutex>
#include <condition_variable>
#include <queue>
#include <chrono>

template <typename T>
class ThreadSafeQueue {
private:
    std::queue<T> dataQueue;
    mutable std::mutex mtx;
    std::condition_variable condition;
    
public:
    void push(T value) {
        {
            std::lock_guard<std::mutex> lock(mtx);
            dataQueue.push(value);
            std::cout << "Pushed: " << value << std::endl;
        }
        condition.notify_one();
    }
    
    T pop() {
        std::unique_lock<std::mutex> lock(mtx);
        condition.wait(lock, [this] { return !dataQueue.empty(); });
        
        T value = dataQueue.front();
        dataQueue.pop();
        std::cout << "Popped: " << value << std::endl;
        return value;
    }
    
    bool tryPop(T& value) {
        std::lock_guard<std::mutex> lock(mtx);
        if (dataQueue.empty()) {
            return false;
        }
        
        value = dataQueue.front();
        dataQueue.pop();
        std::cout << "Try popped: " << value << std::endl;
        return true;
    }
    
    bool empty() const {
        std::lock_guard<std::mutex> lock(mtx);
        return dataQueue.empty();
    }
    
    size_t size() const {
        std::lock_guard<std::mutex> lock(mtx);
        return dataQueue.size();
    }
};

void producer(ThreadSafeQueue<int>& queue, int id, int count) {
    for (int i = 0; i < count; i++) {
        int value = id * 100 + i;
        queue.push(value);
        std::this_thread::sleep_for(std::chrono::milliseconds(50));
    }
}

void consumer(ThreadSafeQueue<int>& queue, int id, int count) {
    for (int i = 0; i < count; i++) {
        int value = queue.pop();
        std::cout << "Consumer " << id << " got: " << value << std::endl;
        std::this_thread::sleep_for(std::chrono::milliseconds(100));
    }
}

void demonstrateConditionVariables() {
    std::cout << "=== Condition Variables ===" << std::endl;
    
    ThreadSafeQueue<int> queue;
    
    // Start consumers first (they will wait)
    std::thread consumer1(consumer, std::ref(queue), 1, 5);
    std::thread consumer2(consumer, std::ref(queue), 2, 5);
    
    // Start producers
    std::thread producer1(producer, std::ref(queue), 1, 3);
    std::thread producer2(producer, std::ref(queue), 2, 7);
    
    producer1.join();
    producer2.join();
    consumer1.join();
    consumer2.join();
}

// Read-Write Lock example
class ReadWriteLock {
private:
    std::mutex mtx;
    std::condition_variable readCondition;
    std::condition_variable writeCondition;
    int activeReaders = 0;
    bool activeWriter = false;
    int waitingWriters = 0;
    
public:
    void readLock() {
        std::unique_lock<std::mutex> lock(mtx);
        readCondition.wait(lock, [this] { return !activeWriter && waitingWriters == 0; });
        activeReaders++;
    }
    
    void readUnlock() {
        std::unique_lock<std::mutex> lock(mtx);
        activeReaders--;
        if (activeReaders == 0) {
            writeCondition.notify_one();
        }
    }
    
    void writeLock() {
        std::unique_lock<std::mutex> lock(mtx);
        waitingWriters++;
        writeCondition.wait(lock, [this] { return !activeWriter && activeReaders == 0; });
        waitingWriters--;
        activeWriter = true;
    }
    
    void writeUnlock() {
        std::unique_lock<std::mutex> lock(mtx);
        activeWriter = false;
        if (waitingWriters > 0) {
            writeCondition.notify_one();
        } else {
            readCondition.notify_all();
        }
    }
};

class SharedData {
private:
    ReadWriteLock rwLock;
    std::vector<int> data;
    
public:
    void addValue(int value) {
        rwLock.writeLock();
        data.push_back(value);
        std::cout << "Writer " << std::this_thread::get_id() 
                  << " added: " << value << std::endl;
        rwLock.writeUnlock();
    }
    
    void readData() {
        rwLock.readLock();
        std::cout << "Reader " << std::this_thread::get_id() 
                  << " reading data: ";
        for (int val : data) {
            std::cout << val << " ";
        }
        std::cout << std::endl;
        rwLock.readUnlock();
    }
};

void writerFunction(SharedData& sharedData, int start, int count) {
    for (int i = 0; i < count; i++) {
        sharedData.addValue(start + i);
        std::this_thread::sleep_for(std::chrono::milliseconds(100));
    }
}

void readerFunction(SharedData& sharedData, int iterations) {
    for (int i = 0; i < iterations; i++) {
        sharedData.readData();
        std::this_thread::sleep_for(std::chrono::milliseconds(50));
    }
}

void demonstrateReadWriteLock() {
    std::cout << "\n=== Read-Write Lock ===" << std::endl;
    
    SharedData sharedData;
    
    std::thread writer1(writerFunction, std::ref(sharedData), 100, 3);
    std::thread writer2(writerFunction, std::ref(sharedData), 200, 3);
    
    std::thread reader1(readerFunction, std::ref(sharedData), 5);
    std::thread reader2(readerFunction, std::ref(sharedData), 5);
    std::thread reader3(readerFunction, std::ref(sharedData), 5);
    
    writer1.join();
    writer2.join();
    reader1.join();
    reader2.join();
    reader3.join();
}

int main() {
    demonstrateConditionVariables();
    demonstrateReadWriteLock();
    return 0;
}
```

## Atomic Operations

### Atomic Types and Operations
```cpp
#include <iostream>
#include <thread>
#include <atomic>
#include <vector>

class AtomicCounter {
private:
    std::atomic<int> count;
    
public:
    AtomicCounter() : count(0) {}
    
    void increment() {
        count.fetch_add(1, std::memory_order_relaxed);
    }
    
    void decrement() {
        count.fetch_sub(1, std::memory_order_relaxed);
    }
    
    int get() const {
        return count.load(std::memory_order_relaxed);
    }
    
    void set(int value) {
        count.store(value, std::memory_order_relaxed);
    }
    
    bool compareAndSwap(int expected, int desired) {
        return count.compare_exchange_weak(expected, desired);
    }
};

void atomicWorker(AtomicCounter& counter, int increments) {
    for (int i = 0; i < increments; i++) {
        counter.increment();
    }
}

void demonstrateAtomic() {
    std::cout << "=== Atomic Operations ===" << std::endl;
    
    AtomicCounter counter;
    std::vector<std::thread> threads;
    
    // Create multiple threads
    for (int i = 0; i < 10; i++) {
        threads.emplace_back(atomicWorker, std::ref(counter), 1000);
    }
    
    // Wait for all threads
    for (auto& thread : threads) {
        thread.join();
    }
    
    std::cout << "Final count: " << counter.get() << std::endl;
}

// Lock-free stack example
template <typename T>
class LockFreeStack {
private:
    struct Node {
        T data;
        Node* next;
        Node(const T& value) : data(value), next(nullptr) {}
    };
    
    std::atomic<Node*> head;
    
public:
    LockFreeStack() : head(nullptr) {}
    
    void push(const T& value) {
        Node* newNode = new Node(value);
        newNode->next = head.load();
        
        while (!head.compare_exchange_weak(newNode->next, newNode)) {
            // Retry if head changed
        }
    }
    
    bool pop(T& result) {
        Node* oldHead = head.load();
        
        while (oldHead && !head.compare_exchange_weak(oldHead, oldHead->next)) {
            // Retry if head changed
        }
        
        if (oldHead) {
            result = oldHead->data;
            delete oldHead;
            return true;
        }
        
        return false;
    }
    
    ~LockFreeStack() {
        Node* current = head.load();
        while (current) {
            Node* next = current->next;
            delete current;
            current = next;
        }
    }
};

void demonstrateLockFreeStack() {
    std::cout << "\n=== Lock-Free Stack ===" << std::endl;
    
    LockFreeStack<int> stack;
    
    // Push elements
    for (int i = 0; i < 10; i++) {
        stack.push(i);
    }
    
    // Pop elements
    int value;
    while (stack.pop(value)) {
        std::cout << "Popped: " << value << std::endl;
    }
}

// Memory ordering demonstration
void demonstrateMemoryOrdering() {
    std::cout << "\n=== Memory Ordering ===" << std::endl;
    
    std::atomic<bool> x(false);
    std::atomic<bool> y(false);
    
    // Thread 1
    std::thread t1([&]() {
        x.store(true, std::memory_order_release);
        y.store(true, std::memory_order_release);
    });
    
    // Thread 2
    std::thread t2([&]() {
        while (!y.load(std::memory_order_acquire)) {
            // Spin wait
        }
        if (x.load(std::memory_order_acquire)) {
            std::cout << "Thread 2 sees x = true after y = true" << std::endl;
        }
    });
    
    t1.join();
    t2.join();
}

int main() {
    demonstrateAtomic();
    demonstrateLockFreeStack();
    demonstrateMemoryOrdering();
    return 0;
}
```

## Async, Futures, and Promises

### Asynchronous Programming
```cpp
#include <iostream>
#include <future>
#include <thread>
#include <chrono>
#include <vector>

int longRunningFunction(int n) {
    std::cout << "Starting long calculation for " << n << std::endl;
    std::this_thread::sleep_for(std::chrono::seconds(2));
    return n * n;
}

void demonstrateAsync() {
    std::cout << "=== Async and Futures ===" << std::endl;
    
    // Launch async task
    std::future<int> result = std::async(std::launch::async, longRunningFunction, 42);
    
    // Do other work while calculation runs
    std::cout << "Doing other work..." << std::endl;
    std::this_thread::sleep_for(std::chrono::milliseconds(500));
    std::cout << "Still working..." << std::endl;
    
    // Get the result (blocks if not ready)
    int value = result.get();
    std::cout << "Result: " << value << std::endl;
}

void demonstratePromise() {
    std::cout << "\n=== Promise and Future ===" << std::endl;
    
    std::promise<int> promise;
    std::future<int> future = promise.get_future();
    
    std::thread producer([&promise]() {
        std::cout << "Producer thread running..." << std::endl;
        std::this_thread::sleep_for(std::chrono::seconds(1));
        promise.set_value(123);
        std::cout << "Producer set value" << std::endl;
    });
    
    std::thread consumer([&future]() {
        std::cout << "Consumer thread waiting..." << std::endl;
        int value = future.get();
        std::cout << "Consumer got value: " << value << std::endl;
    });
    
    producer.join();
    consumer.join();
}

void demonstrateSharedFuture() {
    std::cout << "\n=== Shared Future ===" << std::endl;
    
    std::promise<std::string> promise;
    std::shared_future<std::string> sharedFuture = promise.get_future().share();
    
    auto consumer = [](std::shared_future<std::string> future, int id) {
        std::cout << "Consumer " << id << " waiting..." << std::endl;
        std::string result = future.get();
        std::cout << "Consumer " << id << " got: " << result << std::endl;
    };
    
    std::thread t1(consumer, sharedFuture, 1);
    std::thread t2(consumer, sharedFuture, 2);
    std::thread t3(consumer, sharedFuture, 3);
    
    std::this_thread::sleep_for(std::chrono::seconds(1));
    promise.set_value("Hello from producer!");
    
    t1.join();
    t2.join();
    t3.join();
}

void demonstratePackagedTask() {
    std::cout << "\n=== Packaged Task ===" << std::endl;
    
    // Create a packaged task
    std::packaged_task<int(int)> task([](int n) {
        std::cout << "Task executing with " << n << std::endl;
        std::this_thread::sleep_for(std::chrono::seconds(1));
        return n * n;
    });
    
    // Get the future
    std::future<int> result = task.get_future();
    
    // Run the task in a separate thread
    std::thread worker(std::move(task), 7);
    
    std::cout << "Main thread continues..." << std::endl;
    
    // Get the result
    int value = result.get();
    std::cout << "Task result: " << value << std::endl;
    
    worker.join();
}

// Parallel quicksort using futures
template <typename Iterator>
void parallelQuickSort(Iterator begin, Iterator end) {
    if (end - begin <= 1000) {
        std::sort(begin, end);
        return;
    }
    
    Iterator middle = begin + (end - begin) / 2;
    std::nth_element(begin, middle, end);
    
    auto leftFuture = std::async(std::launch::async, [begin, middle]() {
        parallelQuickSort(begin, middle);
    });
    
    parallelQuickSort(middle + 1, end);
    leftFuture.wait();
}

void demonstrateParallelSort() {
    std::cout << "\n=== Parallel Sort ===" << std::endl;
    
    std::vector<int> data(10000);
    for (int i = 0; i < 10000; i++) {
        data[i] = rand() % 10000;
    }
    
    auto start = std::chrono::high_resolution_clock::now();
    parallelQuickSort(data.begin(), data.end());
    auto end = std::chrono::high_resolution_clock::now();
    
    auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(end - start);
    std::cout << "Parallel sort took: " << duration.count() << " ms" << std::endl;
    
    // Verify sorted
    bool sorted = std::is_sorted(data.begin(), data.end());
    std::cout << "Data is sorted: " << std::boolalpha << sorted << std::endl;
}

int main() {
    demonstrateAsync();
    demonstratePromise();
    demonstrateSharedFuture();
    demonstratePackagedTask();
    demonstrateParallelSort();
    return 0;
}
```

## Thread Pool Implementation

### Custom Thread Pool
```cpp
#include <iostream>
#include <thread>
#include <vector>
#include <queue>
#include <functional>
#include <mutex>
#include <condition_variable>
#include <future>
#include <atomic>

class ThreadPool {
private:
    std::vector<std::thread> workers;
    std::queue<std::function<void()>> tasks;
    std::mutex queueMutex;
    std::condition_variable condition;
    std::atomic<bool> stop;
    
public:
    ThreadPool(size_t threads) : stop(false) {
        for (size_t i = 0; i < threads; i++) {
            workers.emplace_back([this] {
                while (true) {
                    std::function<void()> task;
                    
                    {
                        std::unique_lock<std::mutex> lock(queueMutex);
                        condition.wait(lock, [this] { 
                            return stop || !tasks.empty(); 
                        });
                        
                        if (stop && tasks.empty()) {
                            return;
                        }
                        
                        task = std::move(tasks.front());
                        tasks.pop();
                    }
                    
                    task();
                }
            });
        }
    }
    
    template <typename F, typename... Args>
    auto enqueue(F&& f, Args&&... args) 
        -> std::future<typename std::result_of<F(Args...)>::type> {
        
        using ReturnType = typename std::result_of<F(Args...)>::type;
        
        auto task = std::make_shared<std::packaged_task<ReturnType()>>(
            std::bind(std::forward<F>(f), std::forward<Args>(args)...)
        );
        
        std::future<ReturnType> result = task->get_future();
        
        {
            std::unique_lock<std::mutex> lock(queueMutex);
            
            if (stop) {
                throw std::runtime_error("enqueue on stopped ThreadPool");
            }
            
            tasks.emplace([task]() { (*task)(); });
        }
        
        condition.notify_one();
        return result;
    }
    
    ~ThreadPool() {
        {
            std::unique_lock<std::mutex> lock(queueMutex);
            stop = true;
        }
        
        condition.notify_all();
        
        for (std::thread& worker : workers) {
            worker.join();
        }
    }
};

void demonstrateThreadPool() {
    std::cout << "=== Thread Pool ===" << std::endl;
    
    ThreadPool pool(4);
    std::vector<std::future<int>> results;
    
    // Enqueue tasks
    for (int i = 0; i < 8; i++) {
        results.emplace_back(
            pool.enqueue([i] {
                std::cout << "Task " << i << " started by thread " 
                          << std::this_thread::get_id() << std::endl;
                
                // Simulate work
                std::this_thread::sleep_for(std::chrono::milliseconds(100 + i * 50));
                
                std::cout << "Task " << i << " completed" << std::endl;
                return i * i;
            })
        );
    }
    
    // Get results
    std::cout << "\nResults:" << std::endl;
    for (auto&& result : results) {
        std::cout << result.get() << " ";
    }
    std::cout << std::endl;
}

// Parallel matrix multiplication using thread pool
class Matrix {
private:
    std::vector<std::vector<int>> data;
    size_t rows, cols;
    
public:
    Matrix(size_t r, size_t c) : rows(r), cols(c), data(r, std::vector<int>(c, 0)) {}
    
    size_t getRows() const { return rows; }
    size_t getCols() const { return cols; }
    
    int& operator()(size_t i, size_t j) { return data[i][j]; }
    const int& operator()(size_t i, size_t j) const { return data[i][j]; }
    
    void randomFill() {
        for (size_t i = 0; i < rows; i++) {
            for (size_t j = 0; j < cols; j++) {
                data[i][j] = rand() % 10;
            }
        }
    }
    
    void print() const {
        for (size_t i = 0; i < rows; i++) {
            for (size_t j = 0; j < cols; j++) {
                std::cout << data[i][j] << " ";
            }
            std::cout << std::endl;
        }
    }
};

Matrix parallelMultiply(const Matrix& A, const Matrix& B, ThreadPool& pool) {
    if (A.getCols() != B.getRows()) {
        throw std::invalid_argument("Matrix dimensions don't match");
    }
    
    Matrix C(A.getRows(), B.getCols());
    std::vector<std::future<void>> futures;
    
    for (size_t i = 0; i < A.getRows(); i++) {
        for (size_t j = 0; j < B.getCols(); j++) {
            futures.emplace_back(
                pool.enqueue([&A, &B, &C, i, j]() {
                    int sum = 0;
                    for (size_t k = 0; k < A.getCols(); k++) {
                        sum += A(i, k) * B(k, j);
                    }
                    C(i, j) = sum;
                })
            );
        }
    }
    
    // Wait for all tasks to complete
    for (auto& future : futures) {
        future.wait();
    }
    
    return C;
}

void demonstrateParallelMatrixMultiplication() {
    std::cout << "\n=== Parallel Matrix Multiplication ===" << std::endl;
    
    const size_t SIZE = 50;
    Matrix A(SIZE, SIZE);
    Matrix B(SIZE, SIZE);
    
    A.randomFill();
    B.randomFill();
    
    ThreadPool pool(8);
    
    auto start = std::chrono::high_resolution_clock::now();
    Matrix C = parallelMultiply(A, B, pool);
    auto end = std::chrono::high_resolution_clock::now();
    
    auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(end - start);
    std::cout << "Parallel multiplication took: " << duration.count() << " ms" << std::endl;
    
    // Print a small portion of the result
    std::cout << "First 3x3 of result:" << std::endl;
    for (size_t i = 0; i < 3 && i < C.getRows(); i++) {
        for (size_t j = 0; j < 3 && j < C.getCols(); j++) {
            std::cout << C(i, j) << " ";
        }
        std::cout << std::endl;
    }
}

int main() {
    demonstrateThreadPool();
    demonstrateParallelMatrixMultiplication();
    return 0;
}
```

## Complete Example: Producer-Consumer System

```cpp
#include <iostream>
#include <thread>
#include <queue>
#include <mutex>
#include <condition_variable>
#include <atomic>
#include <chrono>
#include <random>
#include <vector>

// Task structure
struct Task {
    int id;
    int priority;
    std::string data;
    
    Task(int i, int p, const std::string& d) : id(i), priority(p), data(d) {}
    
    // For priority queue (higher priority = lower number)
    bool operator<(const Task& other) const {
        return priority > other.priority;
    }
};

// Thread-safe priority queue
class TaskQueue {
private:
    std::priority_queue<Task> queue;
    mutable std::mutex mtx;
    std::condition_variable condition;
    std::atomic<bool> shutdown;
    
public:
    TaskQueue() : shutdown(false) {}
    
    void push(const Task& task) {
        {
            std::lock_guard<std::mutex> lock(mtx);
            queue.push(task);
            std::cout << "Task " << task.id << " (priority " << task.priority 
                      << ") enqueued" << std::endl;
        }
        condition.notify_one();
    }
    
    bool pop(Task& task) {
        std::unique_lock<std::mutex> lock(mtx);
        condition.wait(lock, [this] { return shutdown || !queue.empty(); });
        
        if (shutdown && queue.empty()) {
            return false;
        }
        
        task = queue.top();
        queue.pop();
        return true;
    }
    
    void shutdownQueue() {
        shutdown = true;
        condition.notify_all();
    }
    
    size_t size() const {
        std::lock_guard<std::mutex> lock(mtx);
        return queue.size();
    }
};

// Worker class
class Worker {
private:
    int id;
    TaskQueue& taskQueue;
    std::thread workerThread;
    std::atomic<int> tasksProcessed;
    
public:
    Worker(int workerId, TaskQueue& queue) 
        : id(workerId), taskQueue(queue), tasksProcessed(0) {}
    
    void start() {
        workerThread = std::thread([this]() { run(); });
    }
    
    void stop() {
        if (workerThread.joinable()) {
            workerThread.join();
        }
    }
    
    int getTasksProcessed() const {
        return tasksProcessed.load();
    }
    
private:
    void run() {
        std::cout << "Worker " << id << " started" << std::endl;
        
        Task task(0, 0, "");
        while (taskQueue.pop(task)) {
            processTask(task);
            tasksProcessed++;
        }
        
        std::cout << "Worker " << id << " stopped. Processed " 
                  << tasksProcessed.load() << " tasks" << std::endl;
    }
    
    void processTask(const Task& task) {
        std::cout << "Worker " << id << " processing task " << task.id 
                  << " (priority " << task.priority << ")" << std::endl;
        
        // Simulate work based on priority
        int workTime = (5 - task.priority) * 100 + 50;
        std::this_thread::sleep_for(std::chrono::milliseconds(workTime));
        
        std::cout << "Worker " << id << " completed task " << task.id << std::endl;
    }
};

// Producer class
class Producer {
private:
    int id;
    TaskQueue& taskQueue;
    std::thread producerThread;
    std::atomic<bool> running;
    int tasksProduced;
    
public:
    Producer(int producerId, TaskQueue& queue) 
        : id(producerId), taskQueue(queue), running(false), tasksProduced(0) {}
    
    void start(int taskCount) {
        running = true;
        producerThread = std::thread([this, taskCount]() { run(taskCount); });
    }
    
    void stop() {
        running = false;
        if (producerThread.joinable()) {
            producerThread.join();
        }
    }
    
    int getTasksProduced() const {
        return tasksProduced;
    }
    
private:
    void run(int taskCount) {
        std::cout << "Producer " << id << " started" << std::endl;
        
        std::random_device rd;
        std::mt19937 gen(rd());
        std::uniform_int_distribution<> priorityDist(1, 5);
        std::uniform_int_distribution<> delayDist(100, 500);
        
        for (int i = 0; i < taskCount && running; i++) {
            int priority = priorityDist(gen);
            Task task(id * 1000 + i, priority, "Data from producer " + std::to_string(id));
            
            taskQueue.push(task);
            tasksProduced++;
            
            // Random delay between tasks
            std::this_thread::sleep_for(std::chrono::milliseconds(delayDist(gen)));
        }
        
        std::cout << "Producer " << id << " finished. Produced " 
                  << tasksProduced << " tasks" << std::endl;
    }
};

// System monitor
class SystemMonitor {
private:
    TaskQueue& taskQueue;
    std::vector<std::unique_ptr<Worker>>& workers;
    std::vector<std::unique_ptr<Producer>>& producers;
    std::thread monitorThread;
    std::atomic<bool> running;
    
public:
    SystemMonitor(TaskQueue& queue, 
                 std::vector<std::unique_ptr<Worker>>& w,
                 std::vector<std::unique_ptr<Producer>>& p)
        : taskQueue(queue), workers(w), producers(p), running(false) {}
    
    void start() {
        running = true;
        monitorThread = std::thread([this]() { run(); });
    }
    
    void stop() {
        running = false;
        if (monitorThread.joinable()) {
            monitorThread.join();
        }
    }
    
private:
    void run() {
        while (running) {
            std::this_thread::sleep_for(std::chrono::seconds(2));
            
            std::cout << "\n=== System Status ===" << std::endl;
            std::cout << "Queue size: " << taskQueue.size() << std::endl;
            
            int totalProcessed = 0;
            for (const auto& worker : workers) {
                totalProcessed += worker->getTasksProcessed();
            }
            std::cout << "Total tasks processed: " << totalProcessed << std::endl;
            
            int totalProduced = 0;
            for (const auto& producer : producers) {
                totalProduced += producer->getTasksProduced();
            }
            std::cout << "Total tasks produced: " << totalProduced << std::endl;
            std::cout << "===================" << std::endl;
        }
    }
};

// Main system class
class ProducerConsumerSystem {
private:
    TaskQueue taskQueue;
    std::vector<std::unique_ptr<Worker>> workers;
    std::vector<std::unique_ptr<Producer>> producers;
    std::unique_ptr<SystemMonitor> monitor;
    
public:
    ProducerConsumerSystem(int numWorkers, int numProducers) {
        // Create workers
        for (int i = 0; i < numWorkers; i++) {
            workers.push_back(std::make_unique<Worker>(i + 1, taskQueue));
        }
        
        // Create producers
        for (int i = 0; i < numProducers; i++) {
            producers.push_back(std::make_unique<Producer>(i + 1, taskQueue));
        }
        
        // Create monitor
        monitor = std::make_unique<SystemMonitor>(taskQueue, workers, producers);
    }
    
    void start(int tasksPerProducer) {
        std::cout << "=== Starting Producer-Consumer System ===" << std::endl;
        
        // Start monitor
        monitor->start();
        
        // Start workers
        for (auto& worker : workers) {
            worker->start();
        }
        
        // Start producers
        for (auto& producer : producers) {
            producer->start(tasksPerProducer);
        }
        
        std::cout << "System started with " << workers.size() << " workers and " 
                  << producers.size() << " producers" << std::endl;
    }
    
    void stop() {
        std::cout << "\n=== Stopping System ===" << std::endl;
        
        // Stop producers
        for (auto& producer : producers) {
            producer->stop();
        }
        
        // Wait for queue to empty
        while (taskQueue.size() > 0) {
            std::this_thread::sleep_for(std::chrono::milliseconds(100));
        }
        
        // Shutdown queue and stop workers
        taskQueue.shutdownQueue();
        for (auto& worker : workers) {
            worker->stop();
        }
        
        // Stop monitor
        monitor->stop();
        
        // Print final statistics
        printStatistics();
    }
    
    void printStatistics() {
        std::cout << "\n=== Final Statistics ===" << std::endl;
        
        int totalProcessed = 0;
        for (const auto& worker : workers) {
            int processed = worker->getTasksProcessed();
            totalProcessed += processed;
            std::cout << "Worker " << worker.get() << " processed " << processed << " tasks" << std::endl;
        }
        
        int totalProduced = 0;
        for (const auto& producer : producers) {
            int produced = producer->getTasksProduced();
            totalProduced += produced;
            std::cout << "Producer " << producer.get() << " produced " << produced << " tasks" << std::endl;
        }
        
        std::cout << "Total tasks produced: " << totalProduced << std::endl;
        std::cout << "Total tasks processed: " << totalProcessed << std::endl;
        std::cout << "Tasks remaining in queue: " << taskQueue.size() << std::endl;
        std::cout << "System efficiency: " 
                  << (totalProduced > 0 ? (100.0 * totalProcessed / totalProduced) : 0) 
                  << "%" << std::endl;
    }
};

int main() {
    // Create system with 3 workers and 2 producers
    ProducerConsumerSystem system(3, 2);
    
    // Start system (each producer produces 10 tasks)
    system.start(10);
    
    // Let it run for 15 seconds
    std::this_thread::sleep_for(std::chrono::seconds(15));
    
    // Stop system
    system.stop();
    
    std::cout << "\nSystem shutdown complete!" << std::endl;
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Parallel Map-Reduce
Implement a parallel map-reduce framework:
- Map function for data transformation
- Reduce function for aggregation
- Thread pool for parallel execution
- Load balancing and work distribution

### Exercise 2: Concurrent Data Structures
Create thread-safe data structures:
- Concurrent hash map
- Lock-free queue
- Read-optimized balanced tree
- Memory reclamation for lock-free structures

### Exercise 3: Dining Philosophers Problem
Solve the classic dining philosophers problem:
- Multiple philosophers and chopsticks
- Deadlock prevention strategies
- Fairness and starvation prevention
- Performance optimization

### Exercise 4: Parallel Web Server
Build a simple parallel web server:
- Thread pool for request handling
- Asynchronous I/O operations
- Connection management
- Load testing and benchmarking

## Key Takeaways
- Multithreading enables concurrent execution on multi-core systems
- std::thread provides basic thread management
- Synchronization primitives prevent race conditions
- Mutexes protect shared data from concurrent access
- Condition variables enable thread coordination
- Atomic operations provide lock-free programming
- Futures and promises support asynchronous programming
- Thread pools improve performance and resource management
- Always consider thread safety in concurrent applications

## Next Module
In the next module, we'll explore network programming and sockets in C++.