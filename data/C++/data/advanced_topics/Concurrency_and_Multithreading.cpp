// Module 13: Concurrency and Multithreading - Real-Life Examples
// This file demonstrates practical applications of concurrency and multithreading

#include <iostream>
#include <thread>
#include <mutex>
#include <condition_variable>
#include <atomic>
#include <future>
#include <queue>
#include <vector>
#include <memory>
#include <chrono>
#include <random>
#include <functional>

// Example 1: Thread-Safe Bank Account System
class ThreadSafeBankAccount {
private:
    mutable std::mutex mtx;
    double balance;
    std::string accountNumber;
    
public:
    ThreadSafeBankAccount(const std::string& accNum, double initialBalance)
        : accountNumber(accNum), balance(initialBalance) {}
    
    void deposit(double amount) {
        std::lock_guard<std::mutex> lock(mtx);
        if (amount > 0) {
            balance += amount;
            std::cout << "Deposited $" << amount << " to " << accountNumber 
                      << ". New balance: $" << balance << std::endl;
        }
    }
    
    bool withdraw(double amount) {
        std::lock_guard<std::mutex> lock(mtx);
        if (amount > 0 && amount <= balance) {
            balance -= amount;
            std::cout << "Withdrew $" << amount << " from " << accountNumber 
                      << ". New balance: $" << balance << std::endl;
            return true;
        }
        std::cout << "Failed to withdraw $" << amount << " from " << accountNumber 
                  << ". Insufficient funds." << std::endl;
        return false;
    }
    
    double getBalance() const {
        std::lock_guard<std::mutex> lock(mtx);
        return balance;
    }
    
    void transfer(ThreadSafeBankAccount& toAccount, double amount) {
        // Deadlock prevention: always lock accounts in the same order
        std::lock(mtx, toAccount.mtx);
        std::lock_guard<std::mutex> lock1(mtx, std::adopt_lock);
        std::lock_guard<std::mutex> lock2(toAccount.mtx, std::adopt_lock);
        
        if (withdraw(amount)) {
            toAccount.deposit(amount);
            std::cout << "Transferred $" << amount << " from " << accountNumber 
                      << " to " << toAccount.accountNumber << std::endl;
        }
    }
    
    void display() const {
        std::lock_guard<std::mutex> lock(mtx);
        std::cout << "Account " << accountNumber << ": $" << balance << std::endl;
    }
};

// Example 2: Producer-Consumer Pattern
template <typename T>
class ThreadSafeQueue {
private:
    std::queue<T> queue;
    mutable std::mutex mtx;
    std::condition_variable condition;
    std::atomic<bool> shutdown;
    
public:
    ThreadSafeQueue() : shutdown(false) {}
    
    void push(T value) {
        {
            std::lock_guard<std::mutex> lock(mtx);
            queue.push(value);
            std::cout << "Produced: " << value << std::endl;
        }
        condition.notify_one();
    }
    
    bool pop(T& value) {
        std::unique_lock<std::mutex> lock(mtx);
        condition.wait(lock, [this] { return !queue.empty() || shutdown; });
        
        if (shutdown && queue.empty()) {
            return false;
        }
        
        value = queue.front();
        queue.pop();
        std::cout << "Consumed: " << value << std::endl;
        return true;
    }
    
    void shutdown() {
        {
            std::lock_guard<std::mutex> lock(mtx);
            shutdown = true;
        }
        condition.notify_all();
    }
    
    size_t size() const {
        std::lock_guard<std::mutex> lock(mtx);
        return queue.size();
    }
};

class ProducerConsumerSystem {
private:
    ThreadSafeQueue<int> queue;
    std::vector<std::thread> producers;
    std::vector<std::thread> consumers;
    std::atomic<int> itemCount;
    std::atomic<int> producedCount;
    std::atomic<int> consumedCount;
    
public:
    ProducerConsumerSystem() : itemCount(0), producedCount(0), consumedCount(0) {}
    
    void start(int numProducers, int numConsumers, int itemsPerProducer) {
        itemCount.store(itemsPerProducer * numProducers);
        
        // Start producers
        for (int i = 0; i < numProducers; i++) {
            producers.emplace_back([this, itemsPerProducer, i]() {
                std::random_device rd;
                std::mt19937 gen(rd());
                std::uniform_int_distribution<> dis(1, 100);
                
                for (int j = 0; j < itemsPerProducer; j++) {
                    int item = dis(gen);
                    queue.push(item);
                    producedCount++;
                    
                    // Random delay
                    std::this_thread::sleep_for(std::chrono::milliseconds(dis(gen) % 50));
                }
                
                std::cout << "Producer " << i << " finished" << std::endl;
            });
        }
        
        // Start consumers
        for (int i = 0; i < numConsumers; i++) {
            consumers.emplace_back([this, i]() {
                std::random_device rd;
                std::mt19937 gen(rd());
                std::uniform_int_distribution<> dis(10, 100);
                
                while (consumedCount.load() < itemCount.load()) {
                    int item;
                    if (queue.pop(item)) {
                        consumedCount++;
                        
                        // Simulate processing time
                        std::this_thread::sleep_for(std::chrono::milliseconds(dis(gen)));
                    }
                }
                
                std::cout << "Consumer " << i << " finished" << std::endl;
            });
        }
    }
    
    void waitForCompletion() {
        // Wait for all producers to finish
        for (auto& producer : producers) {
            producer.join();
        }
        
        // Signal shutdown to consumers
        queue.shutdown();
        
        // Wait for all consumers to finish
        for (auto& consumer : consumers) {
            consumer.join();
        }
        
        std::cout << "\nProducer-Consumer System Results:" << std::endl;
        std::cout << "Items to produce: " << itemCount.load() << std::endl;
        std::cout << "Items produced: " << producedCount.load() << std::endl;
        std::cout << "Items consumed: " << consumedCount.load() << std::endl;
    }
};

// Example 3: Thread Pool
class ThreadPool {
private:
    std::vector<std::thread> workers;
    std::queue<std::function<void()>> tasks;
    std::mutex queueMutex;
    std::condition_variable condition;
    std::atomic<bool> stop;
    
public:
    ThreadPool(size_t numThreads) : stop(false) {
        for (size_t i = 0; i < numThreads; ++i) {
            workers.emplace_back([this] {
                while (true) {
                    std::function<void()> task;
                    
                    {
                        std::unique_lock<std::mutex> lock(queueMutex);
                        condition.wait(lock, [this] { return stop || !tasks.empty(); });
                        
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
    auto enqueue(F&& f, Args&&... args) -> std::future<decltype(f(args...))> {
        using ReturnType = decltype(f(args...));
        
        auto task = std::make_shared<std::packaged_task<ReturnType()>>(
            std::bind(std::forward<F>(f), std::forward<Args>(args)...)
        );
        
        std::future<ReturnType> result = task->get_future();
        
        {
            std::lock_guard<std::mutex> lock(queueMutex);
            
            if (stop) {
                throw std::runtime_error("Cannot enqueue task on stopped thread pool");
            }
            
            tasks.emplace([task]() { (*task)(); });
        }
        
        condition.notify_one();
        return result;
    }
    
    ~ThreadPool() {
        {
            std::lock_guard<std::mutex> lock(queueMutex);
            stop = true;
        }
        
        condition.notify_all();
        
        for (auto& worker : workers) {
            worker.join();
        }
    }
};

// Example 4: Parallel Matrix Multiplication
class MatrixMultiplier {
private:
    std::vector<std::vector<int>> matrixA;
    std::vector<std::vector<int>> matrixB;
    std::vector<std::vector<int>> result;
    
public:
    MatrixMultiplier(const std::vector<std::vector<int>>& A, 
                    const std::vector<std::vector<int>>& B)
        : matrixA(A), matrixB(B) {
        if (A[0].size() != B.size()) {
            throw std::invalid_argument("Matrix dimensions don't match for multiplication");
        }
        
        // Initialize result matrix with zeros
        result.resize(A.size(), std::vector<int>(B[0].size(), 0));
    }
    
    void multiplySequential() {
        auto start = std::chrono::high_resolution_clock::now();
        
        for (size_t i = 0; i < matrixA.size(); i++) {
            for (size_t j = 0; j < matrixB[0].size(); j++) {
                for (size_t k = 0; k < matrixB.size(); k++) {
                    result[i][j] += matrixA[i][k] * matrixB[k][j];
                }
            }
        }
        
        auto end = std::chrono::high_resolution_clock::now();
        auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(end - start);
        
        std::cout << "Sequential multiplication took: " << duration.count() << " ms" << std::endl;
    }
    
    void multiplyParallel(ThreadPool& pool) {
        auto start = std::chrono::high_resolution_clock::now();
        
        std::vector<std::future<void>> futures;
        
        for (size_t i = 0; i < matrixA.size(); i++) {
            for (size_t j = 0; j < matrixB[0].size(); j++) {
                futures.push_back(pool.enqueue([this, i, j]() {
                    int sum = 0;
                    for (size_t k = 0; k < matrixB.size(); k++) {
                        sum += matrixA[i][k] * matrixB[k][j];
                    }
                    result[i][j] = sum;
                }));
            }
        }
        
        // Wait for all tasks to complete
        for (auto& future : futures) {
            future.wait();
        }
        
        auto end = std::chrono::high_resolution_clock::now();
        auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(end - start);
        
        std::cout << "Parallel multiplication took: " << duration.count() << " ms" << std::endl;
    }
    
    void displayResult() const {
        std::cout << "\nResult Matrix:" << std::endl;
        for (const auto& row : result) {
            for (int val : row) {
                std::cout << std::setw(4) << val;
            }
            std::cout << std::endl;
        }
    }
};

// Example 5: Parallel Web Server Simulation
class WebServer {
private:
    struct Request {
        int id;
        std::string url;
        std::string method;
        std::chrono::system_clock::time_point receivedTime;
        
        Request(int id, const std::string& url, const std::string& method)
            : id(id), url(url), method(method), receivedTime(std::chrono::system_clock::now()) {}
    };
    
    ThreadSafeQueue<Request> requestQueue;
    ThreadPool threadPool;
    std::atomic<int> requestCounter;
    std::atomic<int> processedRequests;
    std::atomic<bool> running;
    
public:
    WebServer(size_t numThreads) : threadPool(numThreads), requestCounter(0), 
                                   processedRequests(0), running(false) {}
    
    void start() {
        running = true;
        std::cout << "Web server started with " << threadPool.get_thread_count() 
                  << " worker threads" << std::endl;
        
        // Start worker threads
        for (size_t i = 0; i < threadPool.get_thread_count(); i++) {
            threadPool.enqueue([this, i]() {
                workerThread(i);
            });
        }
    }
    
    void stop() {
        running = false;
        requestQueue.shutdown();
        std::cout << "Web server stopped" << std::endl;
    }
    
    void handleRequest(const std::string& url, const std::string& method) {
        if (!running) {
            std::cout << "Server is not running" << std::endl;
            return;
        }
        
        int requestId = ++requestCounter;
        Request request(requestId, url, method);
        
        // Simulate request queuing
        std::thread([this, request]() {
            requestQueue.push(request);
        }).detach();
    }
    
private:
    void workerThread(int workerId) {
        std::random_device rd;
        std::mt19937 gen(rd());
        std::uniform_int_distribution<> dis(10, 100);
        
        while (running || !requestQueue.empty()) {
            Request request;
            if (requestQueue.pop(request)) {
                // Simulate processing time
                std::this_thread::sleep_for(std::chrono::milliseconds(dis(gen)));
                
                auto now = std::chrono::system_clock::now();
                auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(
                    now - request.receivedTime);
                
                std::cout << "Worker " << workerId << " processed request #" << request.id
                          << " (" << request.method << " " << request.url << ") in "
                          << duration.count() << "ms" << std::endl;
                
                processedRequests++;
            }
        }
        
        std::cout << "Worker " << workerId << " shutting down" << std::endl;
    }
    
    size_t get_thread_count() const {
        return threadPool.get_thread_count();
    }
};

// Example 6: Parallel Prime Number Calculator
class PrimeCalculator {
private:
    std::atomic<bool> stopFlag;
    std::vector<std::thread> threads;
    std::vector<int> primes;
    std::mutex primesMutex;
    
public:
    PrimeCalculator() : stopFlag(false) {}
    
    void calculatePrimes(int maxNumber, int numThreads) {
        primes.clear();
        stopFlag = false;
        
        std::cout << "Calculating primes up to " << maxNumber 
                  << " using " << numThreads << " threads" << std::endl;
        
        auto start = std::chrono::high_resolution_clock::now();
        
        // Divide work among threads
        int rangePerThread = maxNumber / numThreads;
        
        for (int i = 0; i < numThreads; i++) {
            int startNum = i * rangePerThread + 1;
            int endNum = (i == numThreads - 1) ? maxNumber : (i + 1) * rangePerThread;
            
            threads.emplace_back([this, startNum, endNum, i]() {
                findPrimesInRange(startNum, endNum, i);
            });
        }
        
        // Wait for all threads to complete
        for (auto& thread : threads) {
            thread.join();
        }
        
        threads.clear();
        
        auto end = std::chrono::high_resolution_clock::now();
        auto duration = std::chrono::duration_cast<std::chrono::milliseconds>(end - start);
        
        std::cout << "Found " << primes.size() << " primes in " 
                  << duration.count() << " ms" << std::endl;
    }
    
    void stop() {
        stopFlag = true;
        for (auto& thread : threads) {
            if (thread.joinable()) {
                thread.join();
            }
        }
        threads.clear();
    }
    
    void displayPrimes() const {
        std::cout << "\nPrime numbers: ";
        for (size_t i = 0; i < std::min(primes.size(), size_t(20)); i++) {
            std::cout << primes[i] << " ";
        }
        if (primes.size() > 20) {
            std::cout << "...";
        }
        std::cout << std::endl;
    }
    
private:
    bool isPrime(int n) {
        if (n <= 1) return false;
        if (n == 2) return true;
        if (n % 2 == 0) return false;
        
        for (int i = 3; i * i <= n; i += 2) {
            if (n % i == 0) return false;
        }
        return true;
    }
    
    void findPrimesInRange(int start, int end, int threadId) {
        std::vector<int> localPrimes;
        
        for (int i = start; i <= end; i++) {
            if (stopFlag) break;
            
            if (isPrime(i)) {
                localPrimes.push_back(i);
            }
        }
        
        // Merge local results with global results
        std::lock_guard<std::mutex> lock(primesMutex);
        primes.insert(primes.end(), localPrimes.begin(), localPrimes.end());
        
        std::cout << "Thread " << threadId << " found " << localPrimes.size() 
                  << " primes in range " << start << "-" << end << std::endl;
    }
};

int main() {
    std::cout << "=== Concurrency and Multithreading - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of concurrency\n" << std::endl;
    
    // Example 1: Thread-Safe Bank Account
    std::cout << "=== THREAD-SAFE BANK ACCOUNT ===" << std::endl;
    ThreadSafeBankAccount acc1("ACC001", 1000.0);
    ThreadSafeBankAccount acc2("ACC002", 500.0);
    
    acc1.display();
    acc2.display();
    
    // Create multiple threads for concurrent operations
    std::vector<std::thread> threads;
    
    // Concurrent deposits and withdrawals
    threads.emplace_back([&acc1]() {
        for (int i = 0; i < 5; i++) {
            acc1.deposit(100.0);
            std::this_thread::sleep_for(std::chrono::milliseconds(10));
        }
    });
    
    threads.emplace_back([&acc1]() {
        for (int i = 0; i < 3; i++) {
            acc1.withdraw(50.0);
            std::this_thread::sleep_for(std::chrono::milliseconds(15));
        }
    });
    
    threads.emplace_back([&acc1, &acc2]() {
        for (int i = 0; i < 2; i++) {
            acc1.transfer(acc2, 75.0);
            std::this_thread::sleep_for(std::chrono::milliseconds(20));
        }
    });
    
    // Wait for all threads to complete
    for (auto& thread : threads) {
        thread.join();
    }
    
    acc1.display();
    acc2.display();
    
    // Example 2: Producer-Consumer System
    std::cout << "\n=== PRODUCER-CONSUMER SYSTEM ===" << std::endl;
    ProducerConsumerSystem pcSystem;
    pcSystem.start(2, 3, 10);
    pcSystem.waitForCompletion();
    
    // Example 3: Thread Pool
    std::cout << "\n=== THREAD POOL ===" << std::endl;
    ThreadPool pool(4);
    
    std::vector<std::future<int>> futures;
    
    // Submit tasks to thread pool
    for (int i = 0; i < 8; i++) {
        futures.push_back(pool.enqueue([i]() {
            std::this_thread::sleep_for(std::chrono::milliseconds(100));
            std::cout << "Task " << i << " completed" << std::endl;
            return i * i;
        }));
    }
    
    // Get results
    std::cout << "Task results: ";
    for (auto& future : futures) {
        std::cout << future.get() << " ";
    }
    std::cout << std::endl;
    
    // Example 4: Parallel Matrix Multiplication
    std::cout << "\n=== PARALLEL MATRIX MULTIPLICATION ===" << std::endl;
    
    std::vector<std::vector<int>> matrixA = {{1, 2, 3}, {4, 5, 6}, {7, 8, 9}};
    std::vector<std::vector<int>> matrixB = {{9, 8, 7}, {6, 5, 4}, {3, 2, 1}};
    
    MatrixMultiplier multiplier(matrixA, matrixB);
    
    multiplier.multiplySequential();
    multiplier.multiplyParallel(pool);
    multiplier.displayResult();
    
    // Example 5: Web Server Simulation
    std::cout << "\n=== WEB SERVER SIMULATION ===" << std::endl;
    WebServer server(4);
    server.start();
    
    // Simulate incoming requests
    std::thread requestThread([&server]() {
        std::vector<std::string> urls = {"/home", "/about", "/contact", "/products", "/api/users"};
        std::vector<std::string> methods = {"GET", "POST", "PUT", "DELETE"};
        
        std::random_device rd;
        std::mt19937 gen(rd());
        std::uniform_int_distribution<> urlDist(0, urls.size() - 1);
        std::uniform_int_distribution<> methodDist(0, methods.size() - 1);
        
        for (int i = 0; i < 10; i++) {
            server.handleRequest(urls[urlDist(gen)], methods[methodDist(gen)]);
            std::this_thread::sleep_for(std::chrono::milliseconds(50));
        }
    });
    
    requestThread.join();
    
    // Wait for processing
    std::this_thread::sleep_for(std::chrono::seconds(1));
    server.stop();
    
    // Example 6: Parallel Prime Calculator
    std::cout << "\n=== PARALLEL PRIME CALCULATOR ===" << std::endl;
    PrimeCalculator calculator;
    calculator.calculatePrimes(1000, 4);
    calculator.displayPrimes();
    
    std::cout << "\n\n=== CONCURRENCY SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various concurrency concepts:" << std::endl;
    std::cout << "• Thread-safe data structures with mutexes" << std::endl;
    std::cout << "• Producer-consumer pattern with condition variables" << std::endl;
    std::cout << "• Thread pools for efficient task management" << std::endl;
    std::cout << "• Parallel algorithms for performance optimization" << std::endl;
    std::cout << "• Atomic operations for lock-free programming" << std::endl;
    std::cout << "• Futures and promises for asynchronous programming" << std::endl;
    std::cout << "• Deadlock prevention strategies" << std::endl;
    std::cout << "• Load balancing across multiple threads" << std::endl;
    std::cout << "\nConcurrency is essential for building high-performance applications!" << std::endl;
    
    return 0;
}