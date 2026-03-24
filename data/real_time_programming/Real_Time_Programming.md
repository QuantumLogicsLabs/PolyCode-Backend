# Real-Time Programming

This file contains comprehensive real-time programming examples in C, including high-resolution time management, task scheduling, timer systems, event handling, real-time constraints, and scheduling algorithms like Rate Monotonic and Earliest Deadline First.

## 📚 Real-Time Programming Fundamentals

### 🎯 Real-Time Concepts
- **Deterministic Behavior**: Predictable execution timing
- **Deadlines**: Tasks must complete within specified time limits
- **Priorities**: Task importance determines execution order
- **Scheduling**: Algorithms for managing task execution

### ⚡ Real-Time Requirements
- **Timing Constraints**: Hard, firm, and soft real-time constraints
- **Resource Management**: Predictable resource allocation
- **Interrupt Handling**: Fast response to external events
- **Synchronization**: Coordinated access to shared resources

## ⏱️ High-Resolution Time Management

### Time Structures
```c
// High-resolution time structure
typedef struct {
    struct timespec ts;
} HighResTime;

// Time interval structure
typedef struct {
    long seconds;
    long nanoseconds;
} TimeInterval;
```

### Time Functions
```c
// Get current high-resolution time
void getCurrentTime(HighResTime* time) {
    clock_gettime(CLOCK_MONOTONIC, &time->ts);
}

// Convert timespec to total nanoseconds
long long timespecToNanos(const struct timespec* ts) {
    return (long long)ts->tv_sec * NANOSECONDS_PER_SECOND + ts->tv_nsec;
}

// Convert nanoseconds to timespec
void nanosToTimespec(long long nanos, struct timespec* ts) {
    ts->tv_sec = nanos / NANOSECONDS_PER_SECOND;
    ts->tv_nsec = nanos % NANOSECONDS_PER_SECOND;
}

// Add time intervals
void addTimeIntervals(const HighResTime* base, const TimeInterval* interval, HighResTime* result) {
    result->ts.tv_sec = base->ts.tv_sec + interval->seconds;
    result->ts.tv_nsec = base->ts.tv_nsec + interval->nanoseconds;
    
    // Handle overflow
    if (result->ts.tv_nsec >= NANOSECONDS_PER_SECOND) {
        result->ts.tv_sec += result->ts.tv_nsec / NANOSECONDS_PER_SECOND;
        result->ts.tv_nsec = result->ts.tv_nsec % NANOSECONDS_PER_SECOND;
    }
}

// Compare two times
int compareTimes(const HighResTime* time1, const HighResTime* time2) {
    long long nanos1 = timespecToNanos(&time1->ts);
    long long nanos2 = timespecToNanos(&time2->ts);
    
    if (nanos1 < nanos2) return -1;
    if (nanos1 > nanos2) return 1;
    return 0;
}

// Calculate time difference
void timeDifference(const HighResTime* end, const HighResTime* start, TimeInterval* result) {
    long long end_nanos = timespecToNanos(&end->ts);
    long long start_nanos = timespecToNanos(&start->ts);
    long long diff = end_nanos - start_nanos;
    
    result->seconds = diff / NANOSECONDS_PER_SECOND;
    result->nanoseconds = diff % NANOSECONDS_PER_SECOND;
}
```

### Time Formatting
```c
// Convert time interval to string
void timeIntervalToString(const TimeInterval* interval, char* buffer, size_t buffer_size) {
    if (interval->seconds > 0) {
        snprintf(buffer, buffer_size, "%ld.%09ld seconds", 
                interval->seconds, interval->nanoseconds);
    } else {
        snprintf(buffer, buffer_size, "%ld nanoseconds", interval->nanoseconds);
    }
}
```

**Time Management Benefits**:
- **Precision**: Nanosecond-level timing accuracy
- **Monotonic Clock**: Immune to system time changes
- **Arithmetic**: Easy time calculations
- **Comparison**: Simple time comparison operations

## 🔄 Task Management

### Task Structure
```c
// Real-time task structure
typedef struct {
    int id;
    char name[64];
    void (*task_function)(void* arg);
    void* arg;
    TaskState state;
    int priority;
    pthread_t thread;
    pthread_attr_t attr;
    TimeInterval period;
    TimeInterval deadline;
    TimeInterval execution_time;
    int missed_deadlines;
    HighResTime creation_time;
    HighResTime last_activation;
    HighResTime last_completion;
} RealTimeTask;
```

### Task States
```c
typedef enum {
    TASK_STATE_READY = 0,
    TASK_STATE_RUNNING = 1,
    TASK_STATE_BLOCKED = 2,
    TASK_STATE_TERMINATED = 3
} TaskState;
```

### Task Creation
```c
// Create real-time task
RealTimeTask* createRealTimeTask(const char* name, void (*task_function)(void*), 
                                void* arg, int priority) {
    RealTimeTask* task = malloc(sizeof(RealTimeTask));
    if (!task) return NULL;
    
    memset(task, 0, sizeof(RealTimeTask));
    
    task->id = rand(); // Simple ID generation
    strncpy(task->name, name, sizeof(task->name) - 1);
    task->task_function = task_function;
    task->arg = arg;
    task->state = TASK_STATE_READY;
    task->priority = priority;
    getCurrentTime(&task->creation_time);
    
    // Set up thread attributes for real-time scheduling
    pthread_attr_init(&task->attr);
    pthread_attr_setschedpolicy(&task->attr, SCHED_FIFO);
    pthread_attr_setschedparam(&task->attr, &(struct sched_param){priority});
    
    return task;
}
```

### Task Configuration
```c
// Set task period
void setTaskPeriod(RealTimeTask* task, long seconds, long nanoseconds) {
    task->period.seconds = seconds;
    task->period.nanoseconds = nanoseconds;
}

// Set task deadline
void setTaskDeadline(RealTimeTask* task, long seconds, long nanoseconds) {
    task->deadline.seconds = seconds;
    task->deadline.nanoseconds = nanoseconds;
}
```

### Task Execution
```c
// Task wrapper function
void* taskWrapper(void* arg) {
    RealTimeTask* task = (RealTimeTask*)arg;
    
    while (task->state != TASK_STATE_TERMINATED) {
        getCurrentTime(&task->last_activation);
        
        // Execute the task
        HighResTime start_time, end_time;
        getCurrentTime(&start_time);
        
        task->task_function(task->arg);
        
        getCurrentTime(&end_time);
        timeDifference(&end_time, &start_time, &task->execution_time);
        
        getCurrentTime(&task->last_completion);
        
        // Check deadline
        HighResTime deadline_time;
        addTimeIntervals(&task->last_activation, &task->deadline, &deadline_time);
        
        if (compareTimes(&end_time, &deadline_time) > 0) {
            task->missed_deadlines++;
            printf("Task %s missed deadline!\n", task->name);
        }
        
        // Sleep until next period (if periodic)
        if (task->period.seconds > 0 || task->period.nanoseconds > 0) {
            HighResTime next_activation;
            addTimeIntervals(&task->last_activation, &task->period, &next_activation);
            
            HighResTime current_time;
            getCurrentTime(&current_time);
            
            if (compareTimes(&current_time, &next_activation) < 0) {
                TimeInterval sleep_time;
                timeDifference(&next_activation, &current_time, &sleep_time);
                
                struct timespec sleep_ts = {
                    .tv_sec = sleep_time.seconds,
                    .tv_nsec = sleep_time.nanoseconds
                };
                
                nanosleep(&sleep_ts, NULL);
            }
        } else {
            // Non-periodic task, terminate
            task->state = TASK_STATE_TERMINATED;
            break;
        }
    }
    
    return NULL;
}
```

**Task Management Benefits**:
- **Periodic Execution**: Regular task activation
- **Deadline Monitoring**: Track missed deadlines
- **Priority Management**: Task priority handling
- **Statistics**: Execution time tracking

## ⚡ Timer System

### Timer Structure
```c
// Timer structure
typedef struct {
    int id;
    HighResTime start_time;
    TimeInterval interval;
    int periodic;
    int active;
    void (*callback)(int timer_id, void* user_data);
    void* user_data;
} Timer;
```

### Timer Thread
```c
// Timer thread function
void* timerThreadFunction(void* arg) {
    while (1) {
        pthread_mutex_lock(&timer_mutex);
        
        HighResTime current_time;
        getCurrentTime(&current_time);
        
        for (int i = 0; i < timer_count; i++) {
            Timer* timer = &timers[i];
            
            if (timer->active) {
                HighResTime next_trigger;
                addTimeIntervals(&timer->start_time, &timer->interval, &next_trigger);
                
                if (compareTimes(&current_time, &next_trigger) >= 0) {
                    // Timer triggered
                    if (timer->callback) {
                        timer->callback(timer->id, timer->user_data);
                    }
                    
                    if (timer->periodic) {
                        // Update start time for next trigger
                        timer->start_time = next_trigger;
                    } else {
                        // One-shot timer, deactivate
                        timer->active = 0;
                    }
                }
            }
        }
        
        pthread_mutex_unlock(&timer_mutex);
        
        // Sleep for a short time
        usleep(1000); // 1ms
    }
    
    return NULL;
}
```

### Timer Management
```c
// Create timer
int createTimer(long seconds, long nanoseconds, int periodic,
               void (*callback)(int timer_id, void* user_data), void* user_data) {
    pthread_mutex_lock(&timer_mutex);
    
    if (timer_count >= MAX_TIMERS) {
        pthread_mutex_unlock(&timer_mutex);
        return -1; // Too many timers
    }
    
    Timer* timer = &timers[timer_count];
    timer->id = timer_count;
    getCurrentTime(&timer->start_time);
    timer->interval.seconds = seconds;
    timer->interval.nanoseconds = nanoseconds;
    timer->periodic = periodic;
    timer->active = 1;
    timer->callback = callback;
    timer->user_data = user_data;
    
    timer_count++;
    
    pthread_mutex_unlock(&timer_mutex);
    
    return timer->id;
}

// Stop timer
int stopTimer(int timer_id) {
    pthread_mutex_lock(&timer_mutex);
    
    for (int i = 0; i < timer_count; i++) {
        if (timers[i].id == timer_id) {
            timers[i].active = 0;
            pthread_mutex_unlock(&timer_mutex);
            return 0; // Success
        }
    }
    
    pthread_mutex_unlock(&timer_mutex);
    return -1; // Timer not found
}
```

**Timer System Benefits**:
- **High Precision**: Nanosecond timer accuracy
- **Periodic Support**: Repeating timer events
- **Callback System**: Event-driven timer handling
- **Multiple Timers**: Support for many concurrent timers

## 📡 Event System

### Event Structure
```c
// Event structure
typedef struct {
    int id;
    EventType type;
    HighResTime timestamp;
    void* data;
    size_t data_size;
    int processed;
} Event;

// Event queue
typedef struct {
    Event events[MAX_EVENTS];
    int head;
    int tail;
    int count;
    pthread_mutex_t mutex;
} EventQueue;
```

### Event Queue Management
```c
// Initialize event queue
void initEventQueue(EventQueue* queue) {
    queue->head = 0;
    queue->tail = 0;
    queue->count = 0;
    pthread_mutex_init(&queue->mutex, NULL);
}

// Add event to queue
int enqueueEvent(EventQueue* queue, EventType type, void* data, size_t data_size) {
    pthread_mutex_lock(&queue->mutex);
    
    if (queue->count >= MAX_EVENTS) {
        pthread_mutex_unlock(&queue->mutex);
        return -1; // Queue full
    }
    
    Event* event = &queue->events[queue->tail];
    event->id = rand();
    event->type = type;
    getCurrentTime(&event->timestamp);
    event->processed = 0;
    
    if (data && data_size > 0) {
        event->data = malloc(data_size);
        if (event->data) {
            memcpy(event->data, data, data_size);
            event->data_size = data_size;
        } else {
            event->data_size = 0;
        }
    } else {
        event->data = NULL;
        event->data_size = 0;
    }
    
    queue->tail = (queue->tail + 1) % MAX_EVENTS;
    queue->count++;
    
    pthread_mutex_unlock(&queue->mutex);
    
    return event->id;
}

// Get event from queue
int dequeueEvent(EventQueue* queue, Event* event) {
    pthread_mutex_lock(&queue->mutex);
    
    if (queue->count == 0) {
        pthread_mutex_unlock(&queue->mutex);
        return -1; // Queue empty
    }
    
    *event = queue->events[queue->head];
    queue->head = (queue->head + 1) % MAX_EVENTS;
    queue->count--;
    
    pthread_mutex_unlock(&queue->mutex);
    
    return event->id;
}
```

**Event System Benefits**:
- **Asynchronous Communication**: Non-blocking event handling
- **Type Safety**: Typed event system
- **Timestamping**: Event time tracking
- **Memory Management**: Automatic memory allocation/deallocation

## 🎛️ Real-Time Scheduler

### Scheduler Structure
```c
// Scheduler structure
typedef struct {
    RealTimeTask* current_task;
    TaskQueue ready_queue;
    TaskQueue blocked_queue;
    int running;
    int algorithm; // 0 = Rate Monotonic, 1 = Earliest Deadline First
    pthread_t scheduler_thread;
    pthread_mutex_t mutex;
} RealTimeScheduler;
```

### Scheduling Algorithms

#### Rate Monotonic (RM)
```c
// Rate Monotonic scheduling comparison
int compareTasksRM(const void* a, const void* b) {
    RealTimeTask* task_a = *(RealTimeTask**)a;
    RealTimeTask* task_b = *(RealTimeTask**)b;
    
    // Compare by period (shorter period = higher priority)
    long long period_a = (long long)task_a->period.seconds * NANOSECONDS_PER_SECOND + 
                        task_a->period.nanoseconds;
    long long period_b = (long long)task_b->period.seconds * NANOSECONDS_PER_SECOND + 
                        task_b->period.nanoseconds;
    
    if (period_a < period_b) return -1;
    if (period_a > period_b) return 1;
    return 0;
}
```

#### Earliest Deadline First (EDF)
```c
// Earliest Deadline First scheduling comparison
int compareTasksEDF(const void* a, const void* b) {
    RealTimeTask* task_a = *(RealTimeTask**)a;
    RealTimeTask* task_b = *(RealTimeTask**)b;
    
    // Compare by deadline (earlier deadline = higher priority)
    HighResTime deadline_a, deadline_b;
    addTimeIntervals(&task_a->last_activation, &task_a->deadline, &deadline_a);
    addTimeIntervals(&task_b->last_activation, &task_b->deadline, &deadline_b);
    
    return compareTimes(&deadline_a, &deadline_b);
}
```

### Scheduler Implementation
```c
// Scheduler thread function
void* schedulerThreadFunction(void* arg) {
    while (g_scheduler.running) {
        pthread_mutex_lock(&g_scheduler.mutex);
        
        if (g_scheduler.ready_queue.count > 0) {
            // Sort ready queue based on scheduling algorithm
            if (g_scheduler.algorithm == 0) {
                // Rate Monotonic
                qsort(g_scheduler.ready_queue.tasks, g_scheduler.ready_queue.count, 
                      sizeof(RealTimeTask*), compareTasksRM);
            } else {
                // Earliest Deadline First
                qsort(g_scheduler.ready_queue.tasks, g_scheduler.ready_queue.count, 
                      sizeof(RealTimeTask*), compareTasksEDF);
            }
            
            // Get highest priority task
            RealTimeTask* next_task = g_scheduler.ready_queue.tasks[0];
            
            if (g_scheduler.current_task != next_task) {
                // Context switch
                if (g_scheduler.current_task) {
                    g_scheduler.current_task->state = TASK_STATE_READY;
                }
                
                g_scheduler.current_task = next_task;
                next_task->state = TASK_STATE_RUNNING;
                
                printf("Scheduler: Switched to task %s (priority %d)\n", 
                       next_task->name, next_task->priority);
            }
        }
        
        pthread_mutex_unlock(&g_scheduler.mutex);
        
        // Sleep for short time
        usleep(1000); // 1ms
    }
    
    return NULL;
}
```

**Scheduler Benefits**:
- **Algorithm Support**: Multiple scheduling algorithms
- **Dynamic Scheduling**: Runtime task scheduling
- **Priority Management**: Automatic priority handling
- **Context Switching**: Task switching support

## ⚠️ Real-Time Constraints

### Deadline Monitoring
```c
// Check deadline in task wrapper
void checkDeadline(RealTimeTask* task) {
    HighResTime current_time;
    getCurrentTime(&current_time);
    
    HighResTime deadline_time;
    addTimeIntervals(&task->last_activation, &task->deadline, &deadline_time);
    
    if (compareTimes(&current_time, &deadline_time) > 0) {
        task->missed_deadlines++;
        printf("Task %s missed deadline!\n", task->name);
        
        // Log deadline miss
        char time_str[64];
        timeIntervalToString(&task->execution_time, time_str, sizeof(time_str));
        printf("  Execution time: %s\n", time_str);
    }
}
```

### CPU Utilization Analysis
```c
// Calculate CPU utilization for Rate Monotonic
double calculateRMUtilization(RealTimeTask** tasks, int task_count) {
    double total_utilization = 0.0;
    
    for (int i = 0; i < task_count; i++) {
        RealTimeTask* task = tasks[i];
        
        long long period_nanos = task->period.seconds * NANOSECONDS_PER_SECOND + 
                               task->period.nanoseconds;
        long long exec_nanos = task->execution_time.seconds * NANOSECONDS_PER_SECOND + 
                              task->execution_time.nanoseconds;
        
        total_utilization += (double)exec_nanos / period_nanos;
    }
    
    return total_utilization;
}

// Check schedulability for Rate Monotonic
int isRMSchedulable(RealTimeTask** tasks, int task_count) {
    double utilization = calculateRMUtilization(tasks, task_count);
    
    // Liu/Layland bound
    double bound = task_count * (pow(2.0, 1.0 / task_count) - 1.0);
    
    return utilization <= bound;
}
```

### Worst-Case Execution Time Analysis
```c
// Measure worst-case execution time
void measureWCET(RealTimeTask* task, int iterations) {
    TimeInterval max_time = {0, 0};
    TimeInterval total_time = {0, 0};
    
    for (int i = 0; i < iterations; i++) {
        HighResTime start_time, end_time;
        getCurrentTime(&start_time);
        
        // Execute task
        task->task_function(task->arg);
        
        getCurrentTime(&end_time);
        
        TimeInterval exec_time;
        timeDifference(&end_time, &start_time, &exec_time);
        
        // Update maximum
        if (exec_time.seconds > max_time.seconds || 
            (exec_time.seconds == max_time.seconds && exec_time.nanoseconds > max_time.nanoseconds)) {
            max_time = exec_time;
        }
        
        // Add to total
        total_time.seconds += exec_time.seconds;
        total_time.nanoseconds += exec_time.nanoseconds;
        
        // Handle overflow
        if (total_time.nanoseconds >= NANOSECONDS_PER_SECOND) {
            total_time.seconds += total_time.nanoseconds / NANOSECONDS_PER_SECOND;
            total_time.nanoseconds = total_time.nanoseconds % NANOSECONDS_PER_SECOND;
        }
    }
    
    // Calculate average
    TimeInterval avg_time = {
        .seconds = total_time.seconds / iterations,
        .nanoseconds = total_time.nanoseconds / iterations
    };
    
    printf("WCET Analysis for %s:\n", task->name);
    printf("  Worst-case: %ld.%09ld seconds\n", max_time.seconds, max_time.nanoseconds);
    printf("  Average: %ld.%09ld seconds\n", avg_time.seconds, avg_time.nanoseconds);
}
```

**Constraints Benefits**:
- **Deadline Tracking**: Monitor deadline compliance
- **Utilization Analysis**: CPU usage optimization
- **WCET Measurement**: Worst-case execution time analysis
- **Schedulability**: Predict system schedulability

## 🔧 Interrupt Handling

### Signal Handling
```c
// Signal handler function
void signalHandler(int signum) {
    static int count = 0;
    count++;
    
    printf("Signal %d received (count: %d)\n", signum, count);
    
    // Handle real-time interrupt
    switch (signum) {
        case SIGALRM:
            // Timer interrupt
            handleTimerInterrupt();
            break;
        case SIGUSR1:
            // User-defined interrupt
            handleUserInterrupt();
            break;
        default:
            printf("Unknown signal\n");
            break;
    }
}

// Set up signal handler
void setupSignalHandler(int signum) {
    struct sigaction sa;
    sa.sa_handler = signalHandler;
    sigemptyset(&sa.sa_mask);
    sa.sa_flags = SA_RESTART; // Restart interrupted system calls
    
    if (sigaction(signum, &sa, NULL) == -1) {
        perror("sigaction");
        exit(EXIT_FAILURE);
    }
}
```

### Timer Interrupts
```c
// Set up periodic timer interrupt
void setupTimerInterrupt(long interval_us) {
    struct itimerval timer;
    
    timer.it_interval.tv_sec = interval_us / 1000000;
    timer.it_interval.tv_usec = interval_us % 1000000;
    timer.it_value.tv_sec = interval_us / 1000000;
    timer.it_value.tv_usec = interval_us % 1000000;
    
    if (setitimer(ITIMER_REAL, &timer, NULL) == -1) {
        perror("setitimer");
        exit(EXIT_FAILURE);
    }
    
    printf("Timer interrupt set up for %ld microseconds\n", interval_us);
}

// Handle timer interrupt
void handleTimerInterrupt(void) {
    static int tick_count = 0;
    tick_count++;
    
    // Perform real-time processing
    if (tick_count % 10 == 0) {
        printf("Timer tick %d\n", tick_count);
    }
}
```

**Interrupt Benefits**:
- **Fast Response**: Immediate interrupt handling
- **Periodic Processing**: Regular interrupt-driven tasks
- **Signal Safety**: Safe signal handling
- **Real-Time Response**: Low-latency interrupt processing

## 🔧 Best Practices

### 1. Priority Assignment
```c
// Good: Assign priorities based on criticality
void assignPriorities(RealTimeTask** tasks, int count) {
    // Sort by criticality (highest first)
    for (int i = 0; i < count - 1; i++) {
        for (int j = 0; j < count - i - 1; j++) {
            if (tasks[j]->priority < tasks[j + 1]->priority) {
                RealTimeTask* temp = tasks[j];
                tasks[j] = tasks[j + 1];
                tasks[j + 1] = temp;
            }
        }
    }
    
    // Assign actual priorities (highest = 99, lowest = 1)
    for (int i = 0; i < count; i++) {
        tasks[i]->priority = 99 - i;
    }
}

// Bad: Arbitrary priority assignment
void assignArbitraryPriorities(RealTimeTask* task) {
    task->priority = rand() % 100; // Random priority
}
```

### 2. Deadline Management
```c
// Good: Set reasonable deadlines
void setReasonableDeadlines(RealTimeTask* task) {
    // Deadline should be >= worst-case execution time
    TimeInterval wcet = measureWCET(task, 1000);
    
    // Set deadline to 1.5x WCET
    task->deadline.seconds = wcet.seconds;
    task->deadline.nanoseconds = wcet.nanoseconds * 1.5;
    
    if (task->deadline.nanoseconds >= NANOSECONDS_PER_SECOND) {
        task->deadline.seconds += task->deadline.nanoseconds / NANOSECONDS_PER_SECOND;
        task->deadline.nanoseconds = task->deadline.nanoseconds % NANOSECONDS_PER_SECOND;
    }
}

// Bad: Unrealistic deadlines
void setUnrealisticDeadlines(RealTimeTask* task) {
    task->deadline.seconds = 0;
    task->deadline.nanoseconds = 1000; // 1 microsecond - impossible!
}
```

### 3. Resource Management
```c
// Good: Pre-allocate resources
typedef struct {
    char* buffer;
    size_t size;
    int allocated;
} PreAllocatedResource;

PreAllocatedResource* createPreAllocatedResource(size_t size) {
    PreAllocatedResource* resource = malloc(sizeof(PreAllocatedResource));
    if (!resource) return NULL;
    
    resource->buffer = malloc(size);
    if (!resource->buffer) {
        free(resource);
        return NULL;
    }
    
    resource->size = size;
    resource->allocated = 1;
    
    return resource;
}

// Bad: Dynamic allocation in real-time tasks
void realTimeTaskWithMalloc(void* arg) {
    char* buffer = malloc(1024); // Dangerous in real-time!
    // ... use buffer
    free(buffer);
}
```

### 4. Error Handling
```c
// Good: Graceful error handling
int safeRealTimeOperation(RealTimeTask* task) {
    int result = 0;
    
    // Check preconditions
    if (!task || !task->task_function) {
        return -1;
    }
    
    // Execute with timeout
    if (executeWithTimeout(task, 5000000) != 0) { // 5 second timeout
        printf("Task %s timed out\n", task->name);
        return -2;
    }
    
    return result;
}

// Bad: No error handling
void unsafeRealTimeOperation(RealTimeTask* task) {
    task->task_function(task->arg); // No error checking
}
```

### 5. Timing Analysis
```c
// Good: Comprehensive timing analysis
void analyzeTiming(RealTimeTask* task) {
    // Measure execution times
    TimeInterval min_time = {0, 0}, max_time = {0, 0}, avg_time = {0, 0};
    int iterations = 1000;
    
    for (int i = 0; i < iterations; i++) {
        HighResTime start, end;
        getCurrentTime(&start);
        
        task->task_function(task->arg);
        
        getCurrentTime(&end);
        
        TimeInterval exec_time;
        timeDifference(&end, &start, &exec_time);
        
        // Update statistics
        if (i == 0) {
            min_time = max_time = avg_time = exec_time;
        } else {
            if (compareTimeIntervals(&exec_time, &min_time) < 0) {
                min_time = exec_time;
            }
            if (compareTimeIntervals(&exec_time, &max_time) > 0) {
                max_time = exec_time;
            }
            avg_time = addTimeIntervals(&avg_time, &exec_time);
        }
    }
    
    // Calculate average
    avg_time.seconds /= iterations;
    avg_time.nanoseconds /= iterations;
    
    printf("Timing Analysis for %s:\n", task->name);
    printf("  Min: %ld.%09ld\n", min_time.seconds, min_time.nanoseconds);
    printf("  Max: %ld.%09ld\n", max_time.seconds, max_time.nanoseconds);
    printf("  Avg: %ld.%09ld\n", avg_time.seconds, avg_time.nanoseconds);
}
```

## ⚠️ Common Pitfalls

### 1. Priority Inversion
```c
// Wrong: Priority inversion can occur
pthread_mutex_t shared_mutex;

void highPriorityTask(void* arg) {
    pthread_mutex_lock(&shared_mutex);
    // Critical section
    pthread_mutex_unlock(&shared_mutex);
}

void lowPriorityTask(void* arg) {
    pthread_mutex_lock(&shared_mutex);
    // Long critical section
    sleep(1); // Blocks high priority task!
    pthread_mutex_unlock(&shared_mutex);
}

// Right: Use priority inheritance
pthread_mutexattr_t attr;
pthread_mutexattr_init(&attr);
pthread_mutexattr_setprotocol(&attr, PTHREAD_PRIO_INHERIT);
pthread_mutex_init(&shared_mutex, &attr);
```

### 2. Non-Deterministic Operations
```c
// Wrong: Non-deterministic operations in real-time code
void problematicTask(void* arg) {
    // These operations can block unpredictably
    malloc(1024);           // Can block
    printf("message");        // Can block on I/O
    sleep(1);               // Definitely blocks
    pthread_mutex_lock(&mutex); // Can block
}

// Right: Pre-allocated, non-blocking operations
void deterministicTask(void* arg) {
    // Use pre-allocated resources
    char buffer[1024];       // Stack allocation
    
    // Use lock-free operations
    __sync_fetch_and_add(&counter, 1);
    
    // Avoid blocking operations
    // No malloc, no I/O, no sleep
}
```

### 3. Insufficient Testing
```c
// Wrong: Insufficient testing
void testTask(RealTimeTask* task) {
    task->task_function(task->arg); // Single test
    printf("Task works!\n");
}

// Right: Comprehensive testing
void testTaskComprehensively(RealTimeTask* task) {
    // Test under different loads
    for (int load = 0; load < 100; load += 10) {
        createSystemLoad(load);
        
        // Measure timing
        measureTiming(task, 1000);
        
        // Check deadlines
        testDeadlines(task, 100);
        
        clearSystemLoad();
    }
}
```

### 4. Ignoring WCET
```c
// Wrong: Ignore worst-case execution time
void setDeadlinesWithoutWCET(RealTimeTask* task) {
    task->deadline.seconds = 0;
    task->deadline.nanoseconds = 10000000; // 10ms - arbitrary
}

// Right: Base deadlines on WCET
void setDeadlinesWithWCET(RealTimeTask* task) {
    TimeInterval wcet = measureWCET(task, 1000);
    
    // Set deadline to 1.5x WCET
    task->deadline.seconds = wcet.seconds * 1.5;
    task->deadline.nanoseconds = wcet.nanoseconds * 1.5;
    
    // Handle overflow
    if (task->deadline.nanoseconds >= NANOSECONDS_PER_SECOND) {
        task->deadline.seconds += task->deadline.nanoseconds / NANOSECONDS_PER_SECOND;
        task->deadline.nanoseconds = task->deadline.nanoseconds % NANOSECONDS_PER_SECOND;
    }
}
```

## 🔧 Real-World Applications

### 1. Automotive Control Systems
```c
// Engine control task
void engineControlTask(void* arg) {
    EngineData* data = (EngineData*)arg;
    
    // Read sensors (must complete within 1ms)
    readEngineSensors(data);
    
    // Calculate control outputs (must complete within 2ms)
    calculateControlOutputs(data);
    
    // Update actuators (must complete within 1ms)
    updateActuators(data);
    
    // Total deadline: 5ms
}

// Brake control task (higher priority)
void brakeControlTask(void* arg) {
    BrakeData* data = (BrakeData*)arg;
    
    // Read brake sensors (must complete within 0.5ms)
    readBrakeSensors(data);
    
    // Calculate brake pressure (must complete within 0.5ms)
    calculateBrakePressure(data);
    
    // Apply brakes (must complete within 0.5ms)
    applyBrakes(data);
    
    // Total deadline: 2ms
}
```

### 2. Industrial Automation
```c
// Motion control task
void motionControlTask(void* arg) {
    MotionData* data = (MotionData*)arg;
    
    // Read position encoders (1ms)
    readPositionEncoders(data);
    
    // Calculate trajectory (2ms)
    calculateTrajectory(data);
    
    // Update motor controllers (1ms)
    updateMotorControllers(data);
    
    // Deadline: 5ms, period: 10ms
}

// Safety monitoring task (highest priority)
void safetyMonitorTask(void* arg) {
    SafetyData* data = (SafetyData*)arg;
    
    // Check emergency stops (0.1ms)
    checkEmergencyStops(data);
    
    // Monitor limits (0.1ms)
    monitorLimits(data);
    
    // Trigger safety actions if needed (0.1ms)
    triggerSafetyActions(data);
    
    // Deadline: 1ms, period: 1ms
}
```

### 3. Audio Processing
```c
// Audio processing task
void audioProcessingTask(void* arg) {
    AudioBuffer* buffer = (AudioBuffer*)arg;
    
    // Read audio samples (0.5ms)
    readAudioSamples(buffer);
    
    // Process audio effects (1ms)
    processAudioEffects(buffer);
    
    // Output audio (0.5ms)
    outputAudio(buffer);
    
    // Deadline: 2ms, period: 2ms (for 48kHz, 64 samples)
}

// Video processing task
void videoProcessingTask(void* arg) {
    VideoFrame* frame = (VideoFrame*)arg;
    
    // Capture video frame (5ms)
    captureVideoFrame(frame);
    
    // Process video (10ms)
    processVideoFrame(frame);
    
    // Display frame (5ms)
    displayFrame(frame);
    
    // Deadline: 20ms, period: 33ms (30 FPS)
}
```

## 📚 Further Reading

### Books
- "Real-Time Systems Design Principles" by Hermann Kopetz
- "Hard Real-Time Computing Systems" by Giorgio C. Buttazzo
- "Real-Time Systems" by Jane W.S. Liu

### Topics
- Real-time scheduling theory
- Worst-case execution time analysis
- Real-time operating systems
- Embedded real-time systems
- Real-time communication protocols

Real-time programming in C requires careful consideration of timing constraints, resource management, and system predictability. Master these techniques to build reliable, time-critical systems that meet their deadlines consistently!
