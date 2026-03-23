/*
 * File: mutex_examples.c
 * Description: Mutex synchronization examples
 */

#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>
#include <unistd.h>
#include <time.h>

#define NUM_THREADS 5
#define ITERATIONS 1000000

// Shared counter with mutex
typedef struct {
    int counter;
    pthread_mutex_t mutex;
} SharedCounter;

// Bank account structure
typedef struct {
    double balance;
    pthread_mutex_t mutex;
} BankAccount;

// Initialize shared counter
void init_counter(SharedCounter* counter) {
    counter->counter = 0;
    pthread_mutex_init(&counter->mutex, NULL);
}

// Initialize bank account
void init_account(BankAccount* account, double initial_balance) {
    account->balance = initial_balance;
    pthread_mutex_init(&account->mutex, NULL);
}

// Increment counter without mutex (race condition)
void* increment_without_mutex(void* arg) {
    SharedCounter* counter = (SharedCounter*)arg;
    
    for (int i = 0; i < ITERATIONS; i++) {
        counter->counter++; // Not thread-safe!
    }
    
    return NULL;
}

// Increment counter with mutex (thread-safe)
void* increment_with_mutex(void* arg) {
    SharedCounter* counter = (SharedCounter*)arg;
    
    for (int i = 0; i < ITERATIONS; i++) {
        pthread_mutex_lock(&counter->mutex);
        counter->counter++;
        pthread_mutex_unlock(&counter->mutex);
    }
    
    return NULL;
}

// Deposit money
void deposit(BankAccount* account, double amount) {
    pthread_mutex_lock(&account->mutex);
    
    // Simulate processing time
    usleep(1000);
    
    account->balance += amount;
    printf("Deposited: %.2f, New balance: %.2f\n", amount, account->balance);
    
    pthread_mutex_unlock(&account->mutex);
}

// Withdraw money
int withdraw(BankAccount* account, double amount) {
    pthread_mutex_lock(&account->mutex);
    
    // Simulate processing time
    usleep(1000);
    
    if (account->balance >= amount) {
        account->balance -= amount;
        printf("Withdrew: %.2f, New balance: %.2f\n", amount, account->balance);
        pthread_mutex_unlock(&account->mutex);
        return 1;
    } else {
        printf("Insufficient funds for withdrawal of %.2f\n", amount);
        pthread_mutex_unlock(&account->mutex);
        return 0;
    }
}

// Bank account operations thread
void* bank_operations(void* arg) {
    BankAccount* account = (BankAccount*)arg;
    
    // Random operations
    for (int i = 0; i < 10; i++) {
        if (rand() % 2 == 0) {
            deposit(account, (rand() % 100) + 1);
        } else {
            withdraw(account, (rand() % 100) + 1);
        }
        
        usleep(100000); // 0.1 second
    }
    
    return NULL;
}

// Deadlock example structure
typedef struct {
    pthread_mutex_t mutex1;
    pthread_mutex_t mutex2;
    int resource1;
    int resource2;
} DeadlockDemo;

// Initialize deadlock demo
void init_deadlock_demo(DeadlockDemo* demo) {
    pthread_mutex_init(&demo->mutex1, NULL);
    pthread_mutex_init(&demo->mutex2, NULL);
    demo->resource1 = 1;
    demo->resource2 = 2;
}

// Thread 1: Locks mutex1 then mutex2 (correct order)
void* thread1_safe(void* arg) {
    DeadlockDemo* demo = (DeadlockDemo*)arg;
    
    printf("Thread 1: Trying to lock mutex1\n");
    pthread_mutex_lock(&demo->mutex1);
    printf("Thread 1: Locked mutex1\n");
    
    usleep(100000); // Simulate work
    
    printf("Thread 1: Trying to lock mutex2\n");
    pthread_mutex_lock(&demo->mutex2);
    printf("Thread 1: Locked mutex2\n");
    
    // Use resources
    printf("Thread 1: Using resources %d and %d\n", demo->resource1, demo->resource2);
    usleep(100000);
    
    pthread_mutex_unlock(&demo->mutex2);
    printf("Thread 1: Unlocked mutex2\n");
    
    pthread_mutex_unlock(&demo->mutex1);
    printf("Thread 1: Unlocked mutex1\n");
    
    return NULL;
}

// Thread 2: Locks mutex1 then mutex2 (same order - safe)
void* thread2_safe(void* arg) {
    DeadlockDemo* demo = (DeadlockDemo*)arg;
    
    printf("Thread 2: Trying to lock mutex1\n");
    pthread_mutex_lock(&demo->mutex1);
    printf("Thread 2: Locked mutex1\n");
    
    usleep(100000); // Simulate work
    
    printf("Thread 2: Trying to lock mutex2\n");
    pthread_mutex_lock(&demo->mutex2);
    printf("Thread 2: Locked mutex2\n");
    
    // Use resources
    printf("Thread 2: Using resources %d and %d\n", demo->resource1, demo->resource2);
    usleep(100000);
    
    pthread_mutex_unlock(&demo->mutex2);
    printf("Thread 2: Unlocked mutex2\n");
    
    pthread_mutex_unlock(&demo->mutex1);
    printf("Thread 2: Unlocked mutex1\n");
    
    return NULL;
}

// Thread 3: Locks mutex2 then mutex1 (different order - potential deadlock)
void* thread3_unsafe(void* arg) {
    DeadlockDemo* demo = (DeadlockDemo*)arg;
    
    printf("Thread 3: Trying to lock mutex2\n");
    pthread_mutex_lock(&demo->mutex2);
    printf("Thread 3: Locked mutex2\n");
    
    usleep(100000); // Simulate work
    
    printf("Thread 3: Trying to lock mutex1\n");
    pthread_mutex_lock(&demo->mutex1);
    printf("Thread 3: Locked mutex1\n");
    
    // Use resources
    printf("Thread 3: Using resources %d and %d\n", demo->resource1, demo->resource2);
    usleep(100000);
    
    pthread_mutex_unlock(&demo->mutex1);
    printf("Thread 3: Unlocked mutex1\n");
    
    pthread_mutex_unlock(&demo->mutex2);
    printf("Thread 3: Unlocked mutex2\n");
    
    return NULL;
}

// Reader-writer lock example
typedef struct {
    int data;
    int readers;
    pthread_mutex_t mutex;
    pthread_mutex_t write_mutex;
} ReadWriteLock;

// Initialize read-write lock
void init_rwlock(ReadWriteLock* rwlock) {
    rwlock->data = 0;
    rwlock->readers = 0;
    pthread_mutex_init(&rwlock->mutex, NULL);
    pthread_mutex_init(&rwlock->write_mutex, NULL);
}

// Read lock
void read_lock(ReadWriteLock* rwlock) {
    pthread_mutex_lock(&rwlock->mutex);
    rwlock->readers++;
    if (rwlock->readers == 1) {
        pthread_mutex_lock(&rwlock->write_mutex);
    }
    pthread_mutex_unlock(&rwlock->mutex);
}

// Read unlock
void read_unlock(ReadWriteLock* rwlock) {
    pthread_mutex_lock(&rwlock->mutex);
    rwlock->readers--;
    if (rwlock->readers == 0) {
        pthread_mutex_unlock(&rwlock->write_mutex);
    }
    pthread_mutex_unlock(&rwlock->mutex);
}

// Write lock
void write_lock(ReadWriteLock* rwlock) {
    pthread_mutex_lock(&rwlock->write_mutex);
}

// Write unlock
void write_unlock(ReadWriteLock* rwlock) {
    pthread_mutex_unlock(&rwlock->write_mutex);
}

// Reader thread
void* reader_thread(void* arg) {
    ReadWriteLock* rwlock = (ReadWriteLock*)arg;
    
    for (int i = 0; i < 5; i++) {
        read_lock(rwlock);
        printf("Reader: Reading data = %d\n", rwlock->data);
        usleep(100000); // Simulate reading
        read_unlock(rwlock);
        
        usleep(200000); // Wait between reads
    }
    
    return NULL;
}

// Writer thread
void* writer_thread(void* arg) {
    ReadWriteLock* rwlock = (ReadWriteLock*)arg;
    
    for (int i = 0; i < 5; i++) {
        write_lock(rwlock);
        rwlock->data++;
        printf("Writer: Writing data = %d\n", rwlock->data);
        usleep(200000); // Simulate writing
        write_unlock(rwlock);
        
        usleep(300000); // Wait between writes
    }
    
    return NULL;
}

// Test functions
void test_race_condition() {
    printf("=== Race Condition Test ===\n");
    
    SharedCounter counter;
    init_counter(&counter);
    
    pthread_t threads[NUM_THREADS];
    
    // Create threads without mutex
    for (int i = 0; i < NUM_THREADS; i++) {
        pthread_create(&threads[i], NULL, increment_without_mutex, &counter);
    }
    
    // Wait for threads to complete
    for (int i = 0; i < NUM_THREADS; i++) {
        pthread_join(threads[i], NULL);
    }
    
    printf("Expected: %d, Actual: %d (Race condition occurred!)\n", 
           NUM_THREADS * ITERATIONS, counter.counter);
    
    // Reset and test with mutex
    counter.counter = 0;
    
    for (int i = 0; i < NUM_THREADS; i++) {
        pthread_create(&threads[i], NULL, increment_with_mutex, &counter);
    }
    
    for (int i = 0; i < NUM_THREADS; i++) {
        pthread_join(threads[i], NULL);
    }
    
    printf("Expected: %d, Actual: %d (With mutex: Correct!)\n", 
           NUM_THREADS * ITERATIONS, counter.counter);
    
    pthread_mutex_destroy(&counter.mutex);
}

void test_bank_account() {
    printf("\n=== Bank Account Test ===\n");
    
    BankAccount account;
    init_account(&account, 1000.0);
    
    printf("Initial balance: %.2f\n", account.balance);
    
    pthread_t threads[NUM_THREADS];
    
    // Create threads for bank operations
    for (int i = 0; i < NUM_THREADS; i++) {
        pthread_create(&threads[i], NULL, bank_operations, &account);
    }
    
    // Wait for threads to complete
    for (int i = 0; i < NUM_THREADS; i++) {
        pthread_join(threads[i], NULL);
    }
    
    printf("Final balance: %.2f\n", account.balance);
    
    pthread_mutex_destroy(&account.mutex);
}

void test_deadlock_prevention() {
    printf("\n=== Deadlock Prevention Test ===\n");
    
    DeadlockDemo demo;
    init_deadlock_demo(&demo);
    
    pthread_t thread1, thread2, thread3;
    
    // Create threads with safe locking order
    pthread_create(&thread1, NULL, thread1_safe, &demo);
    pthread_create(&thread2, NULL, thread2_safe, &demo);
    
    // Wait for safe threads
    pthread_join(thread1, NULL);
    pthread_join(thread2, NULL);
    
    printf("Safe threads completed successfully\n");
    
    // Uncomment to test unsafe thread (may cause deadlock)
    /*
    pthread_create(&thread3, NULL, thread3_unsafe, &demo);
    pthread_join(thread3, NULL);
    */
    
    pthread_mutex_destroy(&demo.mutex1);
    pthread_mutex_destroy(&demo.mutex2);
}

void test_read_write_lock() {
    printf("\n=== Read-Write Lock Test ===\n");
    
    ReadWriteLock rwlock;
    init_rwlock(&rwlock);
    
    pthread_t reader_threads[3], writer_thread;
    
    // Create reader threads
    for (int i = 0; i < 3; i++) {
        pthread_create(&reader_threads[i], NULL, reader_thread, &rwlock);
    }
    
    // Create writer thread
    pthread_create(&writer_thread, NULL, writer_thread, &rwlock);
    
    // Wait for all threads
    for (int i = 0; i < 3; i++) {
        pthread_join(reader_threads[i], NULL);
    }
    pthread_join(writer_thread, NULL);
    
    printf("Final data: %d\n", rwlock.data);
    
    pthread_mutex_destroy(&rwlock.mutex);
    pthread_mutex_destroy(&rwlock.write_mutex);
}

int main() {
    srand(time(NULL));
    
    test_race_condition();
    test_bank_account();
    test_deadlock_prevention();
    test_read_write_lock();
    
    printf("\n=== Mutex examples completed ===\n");
    
    return 0;
}
