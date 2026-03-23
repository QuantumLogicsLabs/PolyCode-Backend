/*
 * File: basic_threads.c
 * Description: Basic multithreading examples
 */

#include <stdio.h>
#include <stdlib.h>
#include <pthread.h>
#include <unistd.h>
#include <time.h>

#define NUM_THREADS 5

// Thread function
void* thread_function(void* arg) {
    int thread_id = *(int*)arg;
    
    printf("Thread %d: Starting execution\n", thread_id);
    
    // Simulate work
    for (int i = 0; i < 3; i++) {
        printf("Thread %d: Working... (iteration %d)\n", thread_id, i + 1);
        sleep(1);
    }
    
    printf("Thread %d: Finished execution\n", thread_id);
    
    return NULL;
}

// Thread with return value
void* calculate_sum(void* arg) {
    int n = *(int*)arg;
    int* result = malloc(sizeof(int));
    *result = 0;
    
    for (int i = 1; i <= n; i++) {
        *result += i;
        usleep(100000); // 0.1 second delay
    }
    
    printf("Thread: Sum of 1 to %d = %d\n", n, *result);
    
    return result;
}

// Producer-Consumer example
#define BUFFER_SIZE 5
int buffer[BUFFER_SIZE];
int in = 0, out = 0, count = 0;
pthread_mutex_t mutex;
pthread_cond_t cond_producer, cond_consumer;

void* producer(void* arg) {
    int producer_id = *(int*)arg;
    
    for (int i = 0; i < 10; i++) {
        pthread_mutex_lock(&mutex);
        
        // Wait if buffer is full
        while (count == BUFFER_SIZE) {
            printf("Producer %d: Buffer full, waiting...\n", producer_id);
            pthread_cond_wait(&cond_producer, &mutex);
        }
        
        // Produce item
        buffer[in] = producer_id * 100 + i;
        printf("Producer %d: Produced %d at position %d\n", producer_id, buffer[in], in);
        
        in = (in + 1) % BUFFER_SIZE;
        count++;
        
        // Signal consumer
        pthread_cond_signal(&cond_consumer);
        pthread_mutex_unlock(&mutex);
        
        usleep(200000); // 0.2 second delay
    }
    
    return NULL;
}

void* consumer(void* arg) {
    int consumer_id = *(int*)arg;
    int consumed = 0;
    
    while (consumed < 20) { // Consume total of 20 items
        pthread_mutex_lock(&mutex);
        
        // Wait if buffer is empty
        while (count == 0) {
            printf("Consumer %d: Buffer empty, waiting...\n", consumer_id);
            pthread_cond_wait(&cond_consumer, &mutex);
        }
        
        // Consume item
        int item = buffer[out];
        printf("Consumer %d: Consumed %d from position %d\n", consumer_id, item, out);
        
        out = (out + 1) % BUFFER_SIZE;
        count--;
        consumed++;
        
        // Signal producer
        pthread_cond_signal(&cond_producer);
        pthread_mutex_unlock(&mutex);
        
        usleep(300000); // 0.3 second delay
    }
    
    return NULL;
}

// Matrix multiplication with threads
#define MATRIX_SIZE 3
int matrix_a[MATRIX_SIZE][MATRIX_SIZE];
int matrix_b[MATRIX_SIZE][MATRIX_SIZE];
int result_matrix[MATRIX_SIZE][MATRIX_SIZE];

void* multiply_row(void* arg) {
    int row = *(int*)arg;
    
    for (int col = 0; col < MATRIX_SIZE; col++) {
        result_matrix[row][col] = 0;
        for (int k = 0; k < MATRIX_SIZE; k++) {
            result_matrix[row][col] += matrix_a[row][k] * matrix_b[k][col];
        }
    }
    
    printf("Thread: Calculated row %d\n", row);
    
    return NULL;
}

void print_matrix(int matrix[MATRIX_SIZE][MATRIX_SIZE]) {
    for (int i = 0; i < MATRIX_SIZE; i++) {
        for (int j = 0; j < MATRIX_SIZE; j++) {
            printf("%4d ", matrix[i][j]);
        }
        printf("\n");
    }
}

int main() {
    pthread_t threads[NUM_THREADS];
    int thread_args[NUM_THREADS];
    
    printf("=== Basic Threading Examples ===\n\n");
    
    // Example 1: Basic thread creation
    printf("1. Basic Thread Creation:\n");
    for (int i = 0; i < NUM_THREADS; i++) {
        thread_args[i] = i + 1;
        pthread_create(&threads[i], NULL, thread_function, &thread_args[i]);
    }
    
    // Wait for threads to complete
    for (int i = 0; i < NUM_THREADS; i++) {
        pthread_join(threads[i], NULL);
    }
    
    printf("\n");
    
    // Example 2: Thread with return value
    printf("2. Thread with Return Value:\n");
    pthread_t sum_thread;
    int n = 100;
    pthread_create(&sum_thread, NULL, calculate_sum, &n);
    
    int* sum_result;
    pthread_join(sum_thread, (void**)&sum_result);
    
    printf("Main thread: Received sum = %d\n", *sum_result);
    free(sum_result);
    
    printf("\n");
    
    // Example 3: Producer-Consumer
    printf("3. Producer-Consumer Problem:\n");
    pthread_mutex_init(&mutex, NULL);
    pthread_cond_init(&cond_producer, NULL);
    pthread_cond_init(&cond_consumer, NULL);
    
    pthread_t producer_threads[2], consumer_threads[2];
    int producer_ids[2] = {1, 2};
    int consumer_ids[2] = {1, 2};
    
    // Create producers and consumers
    for (int i = 0; i < 2; i++) {
        pthread_create(&producer_threads[i], NULL, producer, &producer_ids[i]);
        pthread_create(&consumer_threads[i], NULL, consumer, &consumer_ids[i]);
    }
    
    // Wait for completion
    for (int i = 0; i < 2; i++) {
        pthread_join(producer_threads[i], NULL);
        pthread_join(consumer_threads[i], NULL);
    }
    
    pthread_mutex_destroy(&mutex);
    pthread_cond_destroy(&cond_producer);
    pthread_cond_destroy(&cond_consumer);
    
    printf("\n");
    
    // Example 4: Matrix multiplication
    printf("4. Parallel Matrix Multiplication:\n");
    
    // Initialize matrices
    printf("Matrix A:\n");
    for (int i = 0; i < MATRIX_SIZE; i++) {
        for (int j = 0; j < MATRIX_SIZE; j++) {
            matrix_a[i][j] = i + j;
        }
    }
    print_matrix(matrix_a);
    
    printf("\nMatrix B:\n");
    for (int i = 0; i < MATRIX_SIZE; i++) {
        for (int j = 0; j < MATRIX_SIZE; j++) {
            matrix_b[i][j] = i * j + 1;
        }
    }
    print_matrix(matrix_b);
    
    // Create threads for each row
    pthread_t matrix_threads[MATRIX_SIZE];
    int row_args[MATRIX_SIZE];
    
    for (int i = 0; i < MATRIX_SIZE; i++) {
        row_args[i] = i;
        pthread_create(&matrix_threads[i], NULL, multiply_row, &row_args[i]);
    }
    
    // Wait for all threads to complete
    for (int i = 0; i < MATRIX_SIZE; i++) {
        pthread_join(matrix_threads[i], NULL);
    }
    
    printf("\nResult Matrix (A * B):\n");
    print_matrix(result_matrix);
    
    printf("\n=== All threading examples completed ===\n");
    
    return 0;
}
