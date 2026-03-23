/*
 * File: shared_memory.c
 * Description: Shared memory operations for inter-process communication
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/shm.h>
#include <sys/wait.h>
#include <time.h>

#define SHM_SIZE 1024
#define SHM_KEY 1234

// Shared data structure
typedef struct {
    int counter;
    char message[256];
    time_t timestamp;
    pid_t creator_pid;
} SharedData;

// Create shared memory
int create_shared_memory() {
    int shmid = shmget(SHM_KEY, SHM_SIZE, IPC_CREAT | 0666);
    if (shmid == -1) {
        perror("shmget");
        return -1;
    }
    
    printf("Shared memory created: ID = %d, Size = %d bytes\n", shmid, SHM_SIZE);
    return shmid;
}

// Attach to shared memory
SharedData* attach_shared_memory(int shmid) {
    SharedData* data = (SharedData*)shmat(shmid, NULL, 0);
    if (data == (SharedData*)-1) {
        perror("shmat");
        return NULL;
    }
    
    printf("Attached to shared memory at address %p\n", data);
    return data;
}

// Detach from shared memory
int detach_shared_memory(SharedData* data) {
    if (shmdt(data) == -1) {
        perror("shmdt");
        return -1;
    }
    
    printf("Detached from shared memory\n");
    return 0;
}

// Remove shared memory
int remove_shared_memory(int shmid) {
    if (shmctl(shmid, IPC_RMID, NULL) == -1) {
        perror("shmctl");
        return -1;
    }
    
    printf("Shared memory removed\n");
    return 0;
}

// Writer process
void writer_process(int shmid) {
    SharedData* data = attach_shared_memory(shmid);
    if (!data) return;
    
    // Initialize shared data
    data->counter = 0;
    data->creator_pid = getpid();
    strcpy(data->message, "Initial message from writer");
    data->timestamp = time(NULL);
    
    printf("Writer process (PID: %d) initialized shared data\n", getpid());
    
    // Write to shared memory
    for (int i = 0; i < 5; i++) {
        data->counter++;
        snprintf(data->message, sizeof(data->message), 
                "Message #%d from writer process", i + 1);
        data->timestamp = time(NULL);
        
        printf("Writer: Wrote message #%d, counter = %d\n", i + 1, data->counter);
        sleep(1);
    }
    
    detach_shared_memory(data);
}

// Reader process
void reader_process(int shmid) {
    SharedData* data = attach_shared_memory(shmid);
    if (!data) return;
    
    printf("Reader process (PID: %d) attached to shared memory\n", getpid());
    
    // Read from shared memory
    for (int i = 0; i < 10; i++) {
        printf("Reader: counter = %d, message = '%s', time = %s", 
               data->counter, data->message, ctime(&data->timestamp));
        
        sleep(1);
    }
    
    detach_shared_memory(data);
}

// Producer-consumer using shared memory
typedef struct {
    int buffer[10];
    int head;
    int tail;
    int count;
    pid_t producer_pid;
    pid_t consumer_pid;
} SharedBuffer;

void producer_process(int shmid) {
    SharedBuffer* buffer = (SharedBuffer*)attach_shared_memory(shmid);
    if (!buffer) return;
    
    buffer->producer_pid = getpid();
    buffer->head = 0;
    buffer->tail = 0;
    buffer->count = 0;
    
    printf("Producer (PID: %d) started\n", getpid());
    
    for (int i = 0; i < 20; i++) {
        // Wait if buffer is full
        while (buffer->count >= 10) {
            printf("Producer: Buffer full, waiting...\n");
            sleep(1);
        }
        
        // Add item to buffer
        buffer->buffer[buffer->tail] = i;
        buffer->tail = (buffer->tail + 1) % 10;
        buffer->count++;
        
        printf("Producer: Produced item %d, buffer count = %d\n", i, buffer->count);
        sleep(1);
    }
    
    detach_shared_memory(buffer);
}

void consumer_process(int shmid) {
    SharedBuffer* buffer = (SharedBuffer*)attach_shared_memory(shmid);
    if (!buffer) return;
    
    buffer->consumer_pid = getpid();
    
    printf("Consumer (PID: %d) started\n", getpid());
    
    for (int i = 0; i < 20; i++) {
        // Wait if buffer is empty
        while (buffer->count <= 0) {
            printf("Consumer: Buffer empty, waiting...\n");
            sleep(1);
        }
        
        // Remove item from buffer
        int item = buffer->buffer[buffer->head];
        buffer->head = (buffer->head + 1) % 10;
        buffer->count--;
        
        printf("Consumer: Consumed item %d, buffer count = %d\n", item, buffer->count);
        sleep(2);
    }
    
    detach_shared_memory(buffer);
}

// Shared memory with semaphores (simplified)
typedef struct {
    int value;
    pid_t last_writer;
    time_t last_update;
} SharedCounter;

void increment_counter(int shmid) {
    SharedCounter* counter = (SharedCounter*)attach_shared_memory(shmid);
    if (!counter) return;
    
    for (int i = 0; i < 10; i++) {
        // Simulate atomic operation (in real implementation, use semaphores)
        counter->value++;
        counter->last_writer = getpid();
        counter->last_update = time(NULL);
        
        printf("Process %d: Counter = %d\n", getpid(), counter->value);
        sleep(1);
    }
    
    detach_shared_memory(counter);
}

// Memory mapping example
void memory_mapping_example() {
    printf("\n=== Memory Mapping Example ===\n");
    
    // Create a temporary file
    FILE* file = tmpfile();
    if (!file) {
        perror("tmpfile");
        return;
    }
    
    // Write some data to the file
    const char* data = "This is test data for memory mapping example";
    fwrite(data, 1, strlen(data), file);
    fclose(file);
    
    // Get file descriptor
    int fd = fileno(tmpfile());
    
    // Map the file into memory
    void* mapped = mmap(NULL, strlen(data), PROT_READ | PROT_WRITE, MAP_SHARED, fd, 0);
    if (mapped == MAP_FAILED) {
        perror("mmap");
        return;
    }
    
    printf("File mapped at address %p\n", mapped);
    printf("Original data: %s\n", (char*)mapped);
    
    // Modify the mapped memory
    strcpy((char*)mapped, "Modified data in memory");
    printf("Modified data: %s\n", (char*)mapped);
    
    // Unmap the memory
    if (munmap(mapped, strlen(data)) == -1) {
        perror("munmap");
    } else {
        printf("Memory unmapped successfully\n");
    }
    
    // Close and remove the file
    fclose(file);
}

// Test function
void test_shared_memory() {
    printf("=== Shared Memory Test ===\n\n");
    
    // Test 1: Basic shared memory
    printf("1. Basic Shared Memory Test:\n");
    int shmid = create_shared_memory();
    
    if (shmid != -1) {
        // Create writer and reader processes
        pid_t writer_pid = fork();
        
        if (writer_pid == 0) {
            // Child process - writer
            writer_process(shmid);
            exit(0);
        } else {
            // Parent process - create reader
            pid_t reader_pid = fork();
            
            if (reader_pid == 0) {
                // Child process - reader
                sleep(1); // Let writer initialize first
                reader_process(shmid);
                exit(0);
            } else {
                // Parent process - wait for children
                wait(NULL);
                wait(NULL);
            }
        }
        
        remove_shared_memory(shmid);
    }
    
    // Test 2: Producer-consumer
    printf("\n2. Producer-Consumer Test:\n");
    shmid = create_shared_memory();
    
    if (shmid != -1) {
        pid_t producer_pid = fork();
        
        if (producer_pid == 0) {
            producer_process(shmid);
            exit(0);
        } else {
            pid_t consumer_pid = fork();
            
            if (consumer_pid == 0) {
                sleep(1); // Let producer start first
                consumer_process(shmid);
                exit(0);
            } else {
                wait(NULL);
                wait(NULL);
            }
        }
        
        remove_shared_memory(shmid);
    }
    
    // Test 3: Multiple processes with shared counter
    printf("\n3. Multiple Processes Shared Counter:\n");
    shmid = create_shared_memory();
    
    if (shmid != -1) {
        // Initialize counter
        SharedCounter* counter = attach_shared_memory(shmid);
        if (counter) {
            counter->value = 0;
            counter->last_writer = 0;
            counter->last_update = time(NULL);
            detach_shared_memory(counter);
        }
        
        // Create multiple processes
        pid_t pids[3];
        
        for (int i = 0; i < 3; i++) {
            pids[i] = fork();
            
            if (pids[i] == 0) {
                increment_counter(shmid);
                exit(0);
            }
        }
        
        // Wait for all processes
        for (int i = 0; i < 3; i++) {
            wait(NULL);
        }
        
        // Check final counter value
        counter = attach_shared_memory(shmid);
        if (counter) {
            printf("Final counter value: %d\n", counter->value);
            printf("Last writer PID: %d\n", counter->last_writer);
            detach_shared_memory(counter);
        }
        
        remove_shared_memory(shmid);
    }
    
    // Test 4: Memory mapping
    memory_mapping_example();
}

// Interactive shared memory demo
void interactive_shared_memory() {
    printf("\n=== Interactive Shared Memory Demo ===\n");
    printf("This demonstrates shared memory between parent and child processes\n");
    printf("Press Enter to continue...\n");
    getchar();
    
    int shmid = create_shared_memory();
    if (shmid == -1) return;
    
    SharedData* data = attach_shared_memory(shmid);
    if (!data) return;
    
    // Initialize data
    data->counter = 0;
    data->creator_pid = getpid();
    strcpy(data->message, "Interactive demo started");
    data->timestamp = time(NULL);
    
    printf("Parent process initialized shared data\n");
    printf("Press Enter to create child process...\n");
    getchar();
    
    pid_t child_pid = fork();
    
    if (child_pid == 0) {
        // Child process
        data = attach_shared_memory(shmid);
        if (data) {
            printf("Child process: Initial counter = %d\n", data->counter);
            
            for (int i = 0; i < 5; i++) {
                data->counter++;
                snprintf(data->message, sizeof(data->message), 
                        "Child update #%d", i + 1);
                data->timestamp = time(NULL);
                
                printf("Child: Updated counter to %d\n", data->counter);
                sleep(1);
            }
            
            detach_shared_memory(data);
        }
        
        exit(0);
    } else {
        // Parent process
        printf("Parent: Child process created (PID: %d)\n", child_pid);
        printf("Press Enter to check shared data...\n");
        getchar();
        
        printf("Parent: Current counter = %d\n", data->counter);
        printf("Parent: Message = '%s'\n", data->message);
        printf("Parent: Timestamp = %s", ctime(&data->timestamp));
        
        printf("Press Enter to update from parent...\n");
        getchar();
        
        data->counter += 10;
        strcpy(data->message, "Parent update");
        data->timestamp = time(NULL);
        
        printf("Parent: Updated counter to %d\n", data->counter);
        
        wait(NULL);
        
        printf("Parent: Child process finished\n");
        printf("Final counter = %d\n", data->counter);
        printf("Final message = '%s'\n", data->message);
        
        detach_shared_memory(data);
    }
    
    remove_shared_memory(shmid);
}

int main() {
    test_shared_memory();
    interactive_shared_memory();
    
    printf("\n=== Shared memory examples completed ===\n");
    
    return 0;
}
