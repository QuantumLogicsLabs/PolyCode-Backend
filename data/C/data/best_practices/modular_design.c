/*
 * File: modular_design.c
 * Description: Examples of modular design principles in C
 * 
 * This file demonstrates how to structure C code in a modular way,
 * promoting reusability, maintainability, and separation of concerns.
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>

// ============================================================================
// MODULE 1: Data Structures
// ============================================================================

/**
 * @defgroup DataStructures Basic data structures
 * @brief Reusable data structure definitions
 * @{
 */

/**
 * @brief Generic vector structure
 * 
 * A dynamic array that can grow and shrink as needed.
 * This is a fundamental data structure used throughout the application.
 */
typedef struct {
    void** data;          /**< Array of pointers to data */
    size_t size;          /**< Current number of elements */
    size_t capacity;      /**< Maximum number of elements before reallocation */
    size_t element_size;  /**< Size of each element in bytes */
} Vector;

/**
 * @brief Stack structure
 * 
 * LIFO (Last In, First Out) data structure implemented using a vector.
 */
typedef struct {
    Vector* vector;       /**< Internal vector storage */
} Stack;

/**
 * @brief Queue structure
 * 
 * FIFO (First In, First Out) data structure implemented using a vector.
 */
typedef struct {
    Vector* vector;       /**< Internal vector storage */
    size_t front;         /**< Index of front element */
} Queue;

/** @} */

// ============================================================================
// MODULE 2: Vector Operations
// ============================================================================

/**
 * @defgroup VectorOperations Vector manipulation functions
 * @brief Functions for creating and managing vectors
 * @{
 */

/**
 * @brief Creates a new vector with specified initial capacity
 * @param initial_capacity Initial number of elements the vector can hold
 * @param element_size Size of each element in bytes
 * @return Pointer to new vector, or NULL on failure
 */
Vector* vectorCreate(size_t initial_capacity, size_t element_size) {
    Vector* vec = (Vector*)malloc(sizeof(Vector));
    if (vec == NULL) return NULL;
    
    vec->data = (void**)malloc(initial_capacity * sizeof(void*));
    if (vec->data == NULL) {
        free(vec);
        return NULL;
    }
    
    vec->size = 0;
    vec->capacity = initial_capacity;
    vec->element_size = element_size;
    
    return vec;
}

/**
 * @brief Destroys a vector and frees all associated memory
 * @param vec Vector to destroy
 */
void vectorDestroy(Vector* vec) {
    if (vec == NULL) return;
    
    // Free all elements
    for (size_t i = 0; i < vec->size; i++) {
        free(vec->data[i]);
    }
    
    free(vec->data);
    free(vec);
}

/**
 * @brief Resizes the vector's internal capacity
 * @param vec Vector to resize
 * @param new_capacity New capacity for the vector
 * @return 1 on success, 0 on failure
 */
int vectorResize(Vector* vec, size_t new_capacity) {
    if (vec == NULL || new_capacity < vec->size) return 0;
    
    void** new_data = (void**)realloc(vec->data, new_capacity * sizeof(void*));
    if (new_data == NULL) return 0;
    
    vec->data = new_data;
    vec->capacity = new_capacity;
    
    return 1;
}

/**
 * @brief Adds an element to the end of the vector
 * @param vec Vector to add element to
 * @param element Pointer to element to add
 * @return 1 on success, 0 on failure
 */
int vectorPush(Vector* vec, void* element) {
    if (vec == NULL || element == NULL) return 0;
    
    // Resize if necessary
    if (vec->size >= vec->capacity) {
        size_t new_capacity = vec->capacity * 2;
        if (!vectorResize(vec, new_capacity)) return 0;
    }
    
    // Allocate memory for element and copy it
    void* element_copy = malloc(vec->element_size);
    if (element_copy == NULL) return 0;
    
    memcpy(element_copy, element, vec->element_size);
    vec->data[vec->size++] = element_copy;
    
    return 1;
}

/**
 * @brief Removes and returns the last element of the vector
 * @param vec Vector to pop element from
 * @return Pointer to the removed element, or NULL if empty
 */
void* vectorPop(Vector* vec) {
    if (vec == NULL || vec->size == 0) return NULL;
    
    void* element = vec->data[--vec->size];
    return element;
}

/**
 * @brief Gets element at specified index
 * @param vec Vector to get element from
 * @param index Index of element to retrieve
 * @return Pointer to element, or NULL if index is invalid
 */
void* vectorGet(Vector* vec, size_t index) {
    if (vec == NULL || index >= vec->size) return NULL;
    return vec->data[index];
}

/**
 * @brief Gets the current size of the vector
 * @param vec Vector to get size of
 * @return Current number of elements in vector
 */
size_t vectorSize(Vector* vec) {
    return vec ? vec->size : 0;
}

/** @} */

// ============================================================================
// MODULE 3: Stack Operations
// ============================================================================

/**
 * @defgroup StackOperations Stack manipulation functions
 * @brief Functions for creating and managing stacks
 * @{
 */

/**
 * @brief Creates a new stack
 * @param initial_capacity Initial capacity of the underlying vector
 * @param element_size Size of each element in bytes
 * @return Pointer to new stack, or NULL on failure
 */
Stack* stackCreate(size_t initial_capacity, size_t element_size) {
    Stack* stack = (Stack*)malloc(sizeof(Stack));
    if (stack == NULL) return NULL;
    
    stack->vector = vectorCreate(initial_capacity, element_size);
    if (stack->vector == NULL) {
        free(stack);
        return NULL;
    }
    
    return stack;
}

/**
 * @brief Destroys a stack and frees all associated memory
 * @param stack Stack to destroy
 */
void stackDestroy(Stack* stack) {
    if (stack == NULL) return;
    vectorDestroy(stack->vector);
    free(stack);
}

/**
 * @brief Pushes an element onto the stack
 * @param stack Stack to push element onto
 * @param element Pointer to element to push
 * @return 1 on success, 0 on failure
 */
int stackPush(Stack* stack, void* element) {
    return stack ? vectorPush(stack->vector, element) : 0;
}

/**
 * @brief Pops an element from the stack
 * @param stack Stack to pop element from
 * @return Pointer to popped element, or NULL if empty
 */
void* stackPop(Stack* stack) {
    return stack ? vectorPop(stack->vector) : NULL;
}

/**
 * @brief Peeks at the top element without removing it
 * @param stack Stack to peek at
 * @return Pointer to top element, or NULL if empty
 */
void* stackPeek(Stack* stack) {
    if (stack == NULL || vectorSize(stack->vector) == 0) return NULL;
    return vectorGet(stack->vector, vectorSize(stack->vector) - 1);
}

/**
 * @brief Checks if the stack is empty
 * @param stack Stack to check
 * @return 1 if empty, 0 otherwise
 */
int stackIsEmpty(Stack* stack) {
    return stack ? (vectorSize(stack->vector) == 0) : 1;
}

/** @} */

// ============================================================================
// MODULE 4: Queue Operations
// ============================================================================

/**
 * @defgroup QueueOperations Queue manipulation functions
 * @brief Functions for creating and managing queues
 * @{
 */

/**
 * @brief Creates a new queue
 * @param initial_capacity Initial capacity of the underlying vector
 * @param element_size Size of each element in bytes
 * @return Pointer to new queue, or NULL on failure
 */
Queue* queueCreate(size_t initial_capacity, size_t element_size) {
    Queue* queue = (Queue*)malloc(sizeof(Queue));
    if (queue == NULL) return NULL;
    
    queue->vector = vectorCreate(initial_capacity, element_size);
    if (queue->vector == NULL) {
        free(queue);
        return NULL;
    }
    
    queue->front = 0;
    
    return queue;
}

/**
 * @brief Destroys a queue and frees all associated memory
 * @param queue Queue to destroy
 */
void queueDestroy(Queue* queue) {
    if (queue == NULL) return;
    vectorDestroy(queue->vector);
    free(queue);
}

/**
 * @brief Adds an element to the back of the queue
 * @param queue Queue to add element to
 * @param element Pointer to element to add
 * @return 1 on success, 0 on failure
 */
int queueEnqueue(Queue* queue, void* element) {
    return queue ? vectorPush(queue->vector, element) : 0;
}

/**
 * @brief Removes and returns the front element of the queue
 * @param queue Queue to dequeue from
 * @return Pointer to dequeued element, or NULL if empty
 */
void* queueDequeue(Queue* queue) {
    if (queue == NULL || vectorSize(queue->vector) == 0) return NULL;
    
    void* element = vectorGet(queue->vector, queue->front);
    queue->front++;
    
    // Reset front when all elements have been dequeued
    if (queue->front >= vectorSize(queue->vector)) {
        queue->front = 0;
    }
    
    return element;
}

/**
 * @brief Peeks at the front element without removing it
 * @param queue Queue to peek at
 * @return Pointer to front element, or NULL if empty
 */
void* queuePeek(Queue* queue) {
    if (queue == NULL || vectorSize(queue->vector) == 0) return NULL;
    return vectorGet(queue->vector, queue->front);
}

/**
 * @brief Checks if the queue is empty
 * @param queue Queue to check
 * @return 1 if empty, 0 otherwise
 */
int queueIsEmpty(Queue* queue) {
    return queue ? (vectorSize(queue->vector) == 0) : 1;
}

/** @} */

// ============================================================================
// MODULE 5: Utility Functions
// ============================================================================

/**
 * @defgroup UtilityFunctions Helper functions
 * @brief Utility functions for demonstrating the data structures
 * @{
 */

/**
 * @brief Prints an integer vector
 * @param vec Vector of integers to print
 */
void printIntVector(Vector* vec) {
    if (vec == NULL) return;
    
    printf("Vector (size %zu): [", vectorSize(vec));
    for (size_t i = 0; i < vectorSize(vec); i++) {
        int* value = (int*)vectorGet(vec, i);
        printf("%d", *value);
        if (i < vectorSize(vec) - 1) printf(", ");
    }
    printf("]\n");
}

/**
 * @brief Prints an integer stack
 * @param stack Stack of integers to print
 */
void printIntStack(Stack* stack) {
    if (stack == NULL) return;
    
    printf("Stack (size %zu): [", vectorSize(stack->vector));
    for (size_t i = 0; i < vectorSize(stack->vector); i++) {
        int* value = (int*)vectorGet(stack->vector, i);
        printf("%d", *value);
        if (i < vectorSize(stack->vector) - 1) printf(", ");
    }
    printf("]\n");
}

/**
 * @brief Prints an integer queue
 * @param queue Queue of integers to print
 */
void printIntQueue(Queue* queue) {
    if (queue == NULL) return;
    
    printf("Queue (size %zu): [", vectorSize(queue->vector));
    for (size_t i = queue->front; i < vectorSize(queue->vector); i++) {
        int* value = (int*)vectorGet(queue->vector, i);
        printf("%d", *value);
        if (i < vectorSize(queue->vector) - 1) printf(", ");
    }
    printf("]\n");
}

/** @} */

// ============================================================================
// MODULE 6: Demonstration
// ============================================================================

/**
 * @brief Demonstrates the modular data structures
 * 
 * This function shows how all the modular components work together
 * to create a cohesive, reusable system.
 * 
 * @return 0 on successful execution
 */
int main() {
    printf("=== Modular Design Examples ===\n\n");
    
    // Demonstrate Vector
    printf("1. Vector Operations:\n");
    Vector* vec = vectorCreate(5, sizeof(int));
    
    for (int i = 1; i <= 5; i++) {
        vectorPush(vec, &i);
    }
    
    printIntVector(vec);
    
    int* popped = (int*)vectorPop(vec);
    printf("Popped: %d\n", *popped);
    free(popped);
    
    printIntVector(vec);
    vectorDestroy(vec);
    
    // Demonstrate Stack
    printf("\n2. Stack Operations:\n");
    Stack* stack = stackCreate(5, sizeof(int));
    
    for (int i = 10; i <= 15; i++) {
        stackPush(stack, &i);
    }
    
    printIntStack(stack);
    
    int* top = (int*)stackPeek(stack);
    printf("Top element: %d\n", *top);
    
    while (!stackIsEmpty(stack)) {
        int* element = (int*)stackPop(stack);
        printf("Popped: %d\n", *element);
        free(element);
    }
    
    stackDestroy(stack);
    
    // Demonstrate Queue
    printf("\n3. Queue Operations:\n");
    Queue* queue = queueCreate(5, sizeof(int));
    
    for (int i = 20; i <= 25; i++) {
        queueEnqueue(queue, &i);
    }
    
    printIntQueue(queue);
    
    int* front = (int*)queuePeek(queue);
    printf("Front element: %d\n", *front);
    
    while (!queueIsEmpty(queue)) {
        int* element = (int*)queueDequeue(queue);
        printf("Dequeued: %d\n", *element);
        free(element);
    }
    
    queueDestroy(queue);
    
    printf("\n=== Modular design examples completed ===\n");
    
    return 0;
}
