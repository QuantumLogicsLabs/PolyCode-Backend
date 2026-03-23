/*
 * File: thread_pool.c
 * Description: Thread pool implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>
#include <unistd.h>

#define THREAD_POOL_SIZE 4
#define TASK_QUEUE_SIZE 100

// Task structure
typedef struct {
    void (*function)(void* arg);
    void* arg;
} Task;

// Thread pool structure
typedef struct {
    pthread_t threads[THREAD_POOL_SIZE];
    Task task_queue[TASK_QUEUE_SIZE];
    
    int queue_size;
    int queue_front;
    int queue_rear;
    
    int shutdown;
    pthread_mutex_t lock;
    pthread_cond_t notify;
} ThreadPool;

// Initialize thread pool
ThreadPool* thread_pool_create() {
    ThreadPool* pool = (ThreadPool*)malloc(sizeof(ThreadPool));
    if (pool == NULL) return NULL;
    
    pool->queue_size = 0;
    pool->queue_front = 0;
    pool->queue_rear = -1;
    pool->shutdown = 0;
    
    pthread_mutex_init(&(pool->lock), NULL);
    pthread_cond_init(&(pool->notify), NULL);
    
    // Create worker threads
    for (int i = 0; i < THREAD_POOL_SIZE; i++) {
        pthread_create(&(pool->threads[i]), NULL, thread_pool_worker, pool);
    }
    
    return pool;
}

// Worker thread function
void* thread_pool_worker(void* arg) {
    ThreadPool* pool = (ThreadPool*)arg;
    
    while (1) {
        pthread_mutex_lock(&(pool->lock));
        
        // Wait for tasks or shutdown
        while (pool->queue_size == 0 && !pool->shutdown) {
            pthread_cond_wait(&(pool->notify), &(pool->lock));
        }
        
        if (pool->shutdown) {
            pthread_mutex_unlock(&(pool->lock));
            pthread_exit(NULL);
        }
        
        // Get task from queue
        Task task = pool->task_queue[pool->queue_front];
        pool->queue_front = (pool->queue_front + 1) % TASK_QUEUE_SIZE;
        pool->queue_size--;
        
        pthread_mutex_unlock(&(pool->lock));
        
        // Execute task
        task.function(task.arg);
    }
    
    return NULL;
}

// Add task to thread pool
int thread_pool_add_task(ThreadPool* pool, void (*function)(void*), void* arg) {
    if (pool == NULL || function == NULL) return -1;
    
    pthread_mutex_lock(&(pool->lock));
    
    if (pool->queue_size >= TASK_QUEUE_SIZE) {
        pthread_mutex_unlock(&(pool->lock));
        return -1;
    }
    
    // Add task to queue
    pool->queue_rear = (pool->queue_rear + 1) % TASK_QUEUE_SIZE;
    pool->task_queue[pool->queue_rear].function = function;
    pool->task_queue[pool->queue_rear].arg = arg;
    pool->queue_size++;
    
    // Notify worker thread
    pthread_cond_signal(&(pool->notify));
    pthread_mutex_unlock(&(pool->lock));
    
    return 0;
}

// Shutdown thread pool
void thread_pool_shutdown(ThreadPool* pool) {
    if (pool == NULL) return;
    
    pthread_mutex_lock(&(pool->lock));
    pool->shutdown = 1;
    pthread_mutex_unlock(&(pool->lock));
    
    // Wake up all worker threads
    pthread_cond_broadcast(&(pool->notify));
    
    // Wait for all threads to finish
    for (int i = 0; i < THREAD_POOL_SIZE; i++) {
        pthread_join(pool->threads[i], NULL);
    }
    
    pthread_mutex_destroy(&(pool->lock));
    pthread_cond_destroy(&(pool->notify));
    free(pool);
}

// Example task functions
void print_message(void* arg) {
    char* message = (char*)arg;
    printf("Thread %lu: %s\n", pthread_self(), message);
    usleep(100000); // 0.1 second
}

void calculate_factorial(void* arg) {
    int n = *(int*)arg;
    long long result = 1;
    
    for (int i = 1; i <= n; i++) {
        result *= i;
    }
    
    printf("Thread %lu: Factorial of %d = %lld\n", pthread_self(), n, result);
    usleep(50000); // 0.05 second
}

void process_array(void* arg) {
    int* array = (int*)arg;
    int sum = 0;
    
    for (int i = 0; i < 10; i++) {
        sum += array[i];
    }
    
    printf("Thread %lu: Array sum = %d\n", pthread_self(), sum);
    usleep(200000); // 0.2 second
}

// Fibonacci calculation task
typedef struct {
    int n;
    int result;
} FibonacciTask;

void calculate_fibonacci(void* arg) {
    FibonacciTask* task = (FibonacciTask*)arg;
    
    if (task->n <= 1) {
        task->result = task->n;
    } else {
        int a = 0, b = 1, c;
        for (int i = 2; i <= task->n; i++) {
            c = a + b;
            a = b;
            b = c;
        }
        task->result = b;
    }
    
    printf("Thread %lu: Fibonacci(%d) = %d\n", pthread_self(), task->n, task->result);
    usleep(75000); // 0.075 second
}

int main() {
    printf("=== Thread Pool Example ===\n\n");
    
    // Create thread pool
    ThreadPool* pool = thread_pool_create();
    if (pool == NULL) {
        printf("Failed to create thread pool\n");
        return 1;
    }
    
    printf("Thread pool created with %d worker threads\n", THREAD_POOL_SIZE);
    printf("Adding tasks to the pool...\n\n");
    
    // Add various tasks
    char* messages[] = {
        "Hello from task 1",
        "Hello from task 2", 
        "Hello from task 3",
        "Hello from task 4",
        "Hello from task 5"
    };
    
    // Add message printing tasks
    for (int i = 0; i < 5; i++) {
        thread_pool_add_task(pool, print_message, messages[i]);
    }
    
    // Add factorial calculation tasks
    int factorials[] = {5, 7, 10, 12, 15};
    for (int i = 0; i < 5; i++) {
        thread_pool_add_task(pool, calculate_factorial, &factorials[i]);
    }
    
    // Add array processing tasks
    int arrays[3][10] = {
        {1, 2, 3, 4, 5, 6, 7, 8, 9, 10},
        {10, 20, 30, 40, 50, 60, 70, 80, 90, 100},
        {2, 4, 6, 8, 10, 12, 14, 16, 18, 20}
    };
    
    for (int i = 0; i < 3; i++) {
        thread_pool_add_task(pool, process_array, arrays[i]);
    }
    
    // Add Fibonacci calculation tasks
    FibonacciTask fib_tasks[5];
    int fib_numbers[] = {10, 15, 20, 25, 30};
    
    for (int i = 0; i < 5; i++) {
        fib_tasks[i].n = fib_numbers[i];
        thread_pool_add_task(pool, calculate_fibonacci, &fib_tasks[i]);
    }
    
    // Wait a bit for tasks to complete
    printf("Waiting for tasks to complete...\n");
    sleep(3);
    
    // Print results of Fibonacci tasks
    printf("\nFibonacci Results:\n");
    for (int i = 0; i < 5; i++) {
        printf("Fibonacci(%d) = %d\n", fib_tasks[i].n, fib_tasks[i].result);
    }
    
    // Shutdown thread pool
    printf("\nShutting down thread pool...\n");
    thread_pool_shutdown(pool);
    
    printf("Thread pool shutdown complete\n");
    
    return 0;
}
